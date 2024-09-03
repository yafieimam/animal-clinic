<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;
use Image;

class PenerimaanPasienController extends Controller
{
    public $model;
    public $notify;
    public function __construct()
    {
        $this->model  = new Modeler();
        $this->notify  = new NotifyController();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('quick_menu/penerimaan_pasien/penerimaan_pasien');
    }

    public function getAntrian(Request $req)
    {
        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->whereDoesntHave('dokter')
            ->whereHas('Poli', function ($q) use ($req) {
                $q->where('name', $req->poli_id);
            })
            ->orderBy('created_at', 'ASC')
            ->orderBy('kode_pendaftaran', 'ASC')
            ->get();
        return view('quick_menu/penerimaan_pasien/list_antrian', compact('data'));
    }

    public function getDokter(Request $req)
    {
        $data = $this->model->jadwalDokterDetail()
            ->whereHas('jadwalDokter', function ($q) use ($req) {
                $q->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id);
                $q->whereHas('JamPertama', function ($q) use ($req) {
                    $q->where(DB::raw("concat(jam_awal,':',menit_awal)"), '<=', carbon::now()->format('H:i'));
                });

                $q->whereHas('JamTerakhir', function ($q) use ($req) {
                    $q->where(DB::raw("concat(jam_awal,':',menit_awal)"), '>=', carbon::now()->format('H:i'));
                });

                $q->where('hari', convertDayToHari(Carbon::now()->format('l')));
            })
            ->with([
                'JadwalDokter' => function ($q) use ($req) {
                    $q->with([
                        'poli',
                        'branch',
                        'jamPertama',
                        'jamTerakhir',

                    ]);
                }, 'DataDokter' => function ($q) use ($req) {
                    $q->with([
                        'pendaftaran' => function ($q) use ($req) {
                            $q->where('status', 'Waiting');
                            $q->where('tanggal', dateStore());
                        },
                    ]);
                },
            ])
            ->get();

        foreach ($data as $i => $d) {
            $penggantiAwal = $this->model->pindahJadwalJaga()
                ->where('tanggal_tujuan', dateStore())
                ->has('DokterDiminta')
                ->with([
                    'DokterPeminta' => function ($q) use ($req) {
                        $q->with([
                            'pendaftaran' => function ($q) use ($req) {
                                $q->where('status', 'Waiting');
                                $q->where('tanggal', dateStore());
                            },
                        ]);
                    },
                ])
                ->where('jadwal_dokter_tujuan_id', $d->jadwal_dokter_id)
                ->first();

            $penggantiTujuan = $this->model->pindahJadwalJaga()
                ->where('tanggal_awal', dateStore())
                ->has('DokterDiminta')
                ->with([
                    'DokterDiminta' => function ($q) use ($req) {
                        $q->with([
                            'pendaftaran' => function ($q) use ($req) {
                                $q->where('status', 'Waiting');
                                $q->where('tanggal', dateStore());
                            },
                        ]);
                    },
                ])
                ->where('jadwal_dokter_awal_id', $d->jadwal_dokter_id)
                ->first();

            if ($penggantiAwal) {
                $d->pengganti = $penggantiAwal->DokterPeminta;
            } else if ($penggantiTujuan) {
                $d->pengganti = $penggantiTujuan->DokterDiminta;
            } else {
                $d->pengganti = null;
            }
        }

        $dalamPenanganan = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Emergency');
            })
            ->has('dokter')
            ->count();


        $antrian = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Emergency');
            })
            ->whereDoesntHave('dokter')
            ->first();

        $sisaAntrian = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Emergency');
            })
            ->count() - $dalamPenanganan;
        $totalAntrian = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Emergency');
            })
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->count();
        return Response()->json(['status' => 1, 'data' => $data, 'antrian' => $antrian, 'total' => $totalAntrian, 'sisa' => $sisaAntrian]);
    }

    public function datatable(Request $req)
    {
        $data = $this->model->pendaftaran()
            ->where(function ($q) use ($req) {
                $q->where('tanggal', '>=', $req->tanggal_awal);
                $q->where('tanggal', '<=', $req->tanggal_akhir);
                if ($req->user_id_filter != null) {
                    $q->where('dokter', $req->user_id_filter);
                }
                if (Auth::user()->akses('global')) {
                    if ($req->branch_id_filter != null) {
                        $q->where('branch_id', $req->branch_id_filter);
                    }
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->whereHas('RekamMedisPasien')
            ->orderBy('created_at', 'ASC')
            ->get();

        return Datatables::of($data)
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-info btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('kode_rm', function ($data) {
                return $data->RekamMedisPasien != null ? $data->RekamMedisPasien->kode  : "-";
            })
            ->addColumn('pasien', function ($data) {
                return $data->Pasien != null ? $data->Pasien->name  : "-";
            })
            ->addColumn('owner', function ($data) {
                return $data->Owner != null ? $data->Owner->name  : "-";
            })
            ->addColumn('dokter', function ($data) {
                return $data->Dokter != null ? $data->Dokter->name  : "-";
            })
            ->addColumn('berat', function ($data) {
                return $data->RekamMedisPasien != null ? $data->RekamMedisPasien->berat  : "-";
            })
            ->addColumn('suhu', function ($data) {
                return $data->RekamMedisPasien != null ? $data->RekamMedisPasien->suhu  : "-";
            })
            ->addColumn('gejala', function ($data) {
                return $data->RekamMedisPasien != null ? $data->RekamMedisPasien->gejala  : "-";
            })
            ->addColumn('hasil_pemeriksaan', function ($data) {
                return $data->RekamMedisPasien != null ? $data->RekamMedisPasien->hasil_pemeriksaan  : "-";
            })
            ->addColumn('tindakan_bedah', function ($data) {
                if ($data->rekamMedisPasien->tindakan_bedah) {
                    return  '<span class="badge badge-info">Ya</span>';
                } else {
                    return  '<span class="badge badge-danger">Tidak</span>';
                }
            })
            ->addColumn('bius', function ($data) {
                if ($data->rekamMedisPasien->bius) {
                    return  '<span class="badge badge-info">Ya</span>';
                } else {
                    return  '<span class="badge badge-danger">Tidak</span>';
                }
            })
            ->addColumn('rawat_inap', function ($data) {
                if ($data->rekamMedisPasien->rawat_inap) {
                    return  '<span class="badge badge-info">Ya</span>';
                } else {
                    return  '<span class="badge badge-danger">Tidak</span>';
                }
            })
            ->addColumn('rawat_jalan', function ($data) {
                if ($data->rekamMedisPasien->rawat_jalan) {
                    return  '<span class="badge badge-info">Ya</span>';
                } else {
                    return  '<span class="badge badge-danger">Tidak</span>';
                }
            })
            ->addColumn('grooming', function ($data) {
                if ($data->rekamMedisPasien->grooming) {
                    return  '<span class="badge badge-info">Ya</span>';
                } else {
                    return  '<span class="badge badge-danger">Tidak</span>';
                }
            })
            ->addColumn('titip_sehat', function ($data) {
                if ($data->rekamMedisPasien->titip_sehat) {
                    return  '<span class="badge badge-info">Ya</span>';
                } else {
                    return  '<span class="badge badge-danger">Tidak</span>';
                }
            })
            ->addColumn('kamar', function ($data) {
                if ($data->RekamMedisPasien->KamarRawatInapDanBedahDetailFirst) {
                    return $data->RekamMedisPasien->KamarRawatInapDanBedahDetailFirst->KamarRawatInapDanBedah->name;
                } else {
                    return '-';
                }
            })
            ->addColumn('catatan', function ($data) {
                return $data->RekamMedisPasien != null ? $data->RekamMedisPasien->catatan  : "-";
            })
            ->rawColumns([
                'aksi',
                'status',
                'icon',
                'sequence',
                'tindakan_bedah',
                'bius',
                'rawat_inap',
                'rawat_jalan',
                'grooming',
                'titip_sehat',
            ])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req)
    {
        Auth::user()->akses('create', null, true);
        return DB::transaction(function () use ($req) {

            $check = $this->model->pendaftaran()
                ->where('status', 'Waiting')
                ->where('dokter', me())
                ->first();

            if ($check != null) {
                return Response()->json(['status' => 2, 'message' => 'Anda harus menyelesaikan pasien sebelumnya untuk menerima pasien ini. Anda sedang menangani pasien dengan kode ' . $check->kode_pendaftaran . ' tanggal ' . $check->tanggal]);
            }

            $pendaftaran = $this->model->pendaftaran()
                ->where('status', 'Waiting')
                ->whereNull('dokter')
                ->find($req->id);

            if ($pendaftaran) {
                $pendaftaran->update([
                    'dokter' => me(),
                    'jam_pickup' => now(),
                ]);
            } else {
                $pendaftaran = $this->model->pendaftaran()
                    ->find($req->id);
                if ($pendaftaran) {
                    return Response()->json(['status' => 2, 'message' => 'Pasien telah ditangani oleh dokter' . $pendaftaran->Dokter ? $pendaftaran->Dokter->name : $pendaftaran->dokter_id]);
                } else {
                    return Response()->json(['status' => 2, 'message' => 'Data pendaftaran tidak ditemukan.']);
                }
            }

            $this->notify->broadCastPenerimaanPasien($req);
            return Response()->json(['status' => 1, 'message' => 'Berhasil menerima pasien']);
        });
    }

    public function history()
    {
        Auth::user()->akses('view', null, true);
        $dokter = $this->model->user()
            ->whereHas('role', function ($q) {
                $q->where('name', 'Dokter Poli');
            })
            ->get();
        return view('quick_menu/penerimaan_pasien/history_penerimaan_pasien', compact('dokter'));
    }
}

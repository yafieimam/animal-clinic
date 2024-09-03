<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MonitoringAntrianController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('quick_menu/monitoring_antrian/monitoring_antrian');
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
            ->with(['Poli'])
            ->orderBy('created_at', 'ASC')
            ->orderBy('kode_pendaftaran', 'ASC')
            ->get();
        return view('quick_menu/monitoring_antrian/list_antrian', compact('data'));
    }

    public function getDokter(Request $req)
    {
        $data = $this->model->jadwalDokterDetail()
            ->whereHas('jadwalDokter', function ($q) use ($req) {
                if ($req->branch_id != 'undefined') {
                    $q->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id);
                }
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
                        },
                    ]);
                },
            ])
            ->get();

        foreach ($data as $i => $d) {
            $penggantiAwal = $this->model->pindahJadwalJaga()
                ->where('tanggal_tujuan', dateStore())
                ->has('DokterPeminta')
                ->with([
                    'DokterPeminta' => function ($q) use ($req) {
                        $q->with([
                            'pendaftaran' => function ($q) use ($req) {
                                $q->where('status', 'Waiting');
                                // $q->where('tanggal', dateStore());
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
                                // $q->where('tanggal', dateStore());
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

        $antrian = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->has('dokter')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Emergency');
            })
            ->first();

        $dalamPenanganan = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->has('dokter')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Emergency');
            })
            ->count();

        if ($antrian == null) {
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
        }

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
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Emergency');
            })
            ->count();

        return Response()->json(['status' => 1, 'data' => $data, 'antrian' => $antrian, 'total' => $totalAntrian, 'sisa' => $sisaAntrian]);
    }

    public function datatable(Request $req)
    {
        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->where(function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    $q->where('branch_id', $req->branch_id);
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->whereDoesntHave('dokter')
            ->where('status', 'Waiting')
            ->orderBy('created_at', 'ASC')
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('quick_menu/monitoring_antrian/action_button_monitoring_antrian', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-info btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('icon', function ($data) {
                return '<i class="' . $data->icon . ' text-2xl"></i>';
            })
            ->addColumn('pasien', function ($data) {
                $html = '';
                foreach ($data->PendaftaranPasien as $key => $value) {
                    $html .= ($value->pasien ? $value->pasien->name : 'Data Corrupt Dihapus') . '<br>';
                }
                return $html;
            })
            ->addColumn('owner', function ($data) {
                return $data->Owner != null ? $data->Owner->name  : "-";
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('poli', function ($data) {
                return $data->Poli != null ? $data->Poli->name  : "-";
            })
            ->addColumn('lain_lain', function ($data) {
                $tujuan = "";
                foreach ($data->PendaftaranPasien as $key => $d) {
                    $tujuan .= ($d->lain_lain ? $d->lain_lain : '-') . '<br>';
                }
                return $tujuan;
            })
            ->addColumn('status_owner', function ($data) {
                if ($data->status_owner) {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-danger text-center text-white cursor-pointer font-medium">Leave</div>';
                } else {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-success text-center text-white cursor-pointer font-medium">Available</div>';
                }
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'pasien', 'status_owner', 'lain_lain'])
            ->addIndexColumn()
            ->make(true);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            if ($req->param == 'all') {
                $this->model->pendaftaran_pasien()->where('pendaftaran_id', $req->id)
                    ->update([
                        'status' => 'Cancel',
                        'updated_by'    => me(),
                    ]);
                $this->model->pendaftaran()->find($req->id)->update(['status' => 'Cancel']);
            } else {

                $this->model->pendaftaran_pasien()
                    ->where('pendaftaran_id', $req->id)
                    ->where('pasien_id', $req->param)
                    ->update([
                        'status' => 'Cancel',
                        'updated_by'    => me(),
                    ]);

                $check = $this->model->pendaftaran_pasien()
                    ->where('pendaftaran_id', $req->id)
                    ->where('status', 'Belum Diperiksa')
                    ->first();

                $getPasienOrNot = $this->model->pendaftaran_pasien()
                    ->where('pendaftaran_id', $req->id)
                    ->where('status', 'Sudah Diperiksa')
                    ->count();
                if (!$check) {
                    if ($getPasienOrNot > 0) {
                        $this->model->pendaftaran()->find($req->id)->update(['status' => 'Completed']);
                    } else {
                        $this->model->pendaftaran()->find($req->id)->update(['status' => 'Cancel']);
                    }
                }
            }
            return Response()->json(['status' => 1, 'message' => 'Batalkan Antrian Sukses']);
        });
    }

    public function fullscreen(Request $req)
    {
        return view('quick_menu/monitoring_antrian/fullscreen_monitoring_antrian', compact('req'));
    }

    public function getAntrianFullscreen(Request $req)
    {
        $emergencyPeriksa = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->has('dokter')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Emergency');
            })
            ->first();

        $periksaPeriksa = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->has('dokter')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Periksa');
            })
            ->first();

        $sterilPeriksa = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->has('dokter')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Steril');
            })
            ->first();

        $groomingPeriksa = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->has('dokter')
            ->whereHas('poli', function ($q) use ($req) {
                $q->where('name', 'Grooming');
            })
            ->first();

        $emergency = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->whereDoesntHave('dokter')
            ->whereHas('Poli', function ($q) use ($req) {
                $q->where('name', "Emergency");
            })
            ->with(['Poli'])
            ->orderBy('created_at', 'ASC')
            ->orderBy('kode_pendaftaran', 'ASC')
            ->get();

        $periksa = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->whereDoesntHave('dokter')
            ->whereHas('Poli', function ($q) use ($req) {
                $q->where('name', "Periksa");
            })
            ->with(['Poli'])
            ->orderBy('created_at', 'ASC')
            ->orderBy('kode_pendaftaran', 'ASC')
            ->get();

        $steril = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->whereDoesntHave('dokter')
            ->whereHas('Poli', function ($q) use ($req) {
                $q->where('name', "Steril");
            })
            ->with(['Poli'])
            ->orderBy('created_at', 'ASC')
            ->orderBy('kode_pendaftaran', 'ASC')
            ->get();

        $grooming = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->where('branch_id', !Auth::user()->akses('global') ? Auth::user()->branch_id : $req->branch_id)
            ->where('status', 'Waiting')
            ->whereDoesntHave('dokter')
            ->whereHas('Poli', function ($q) use ($req) {
                $q->where('name', "Grooming");
            })
            ->with(['Poli'])
            ->orderBy('created_at', 'ASC')
            ->orderBy('kode_pendaftaran', 'ASC')
            ->get();

        return Response()->json(
            [
                'emergency_periksa' => $emergencyPeriksa,
                'periksa_periksa' => $periksaPeriksa,
                'steril_periksa' => $sterilPeriksa,
                'grooming_periksa' => $groomingPeriksa,
                'emergency' => $emergency,
                'periksa' => $periksa,
                'steril' => $steril,
                'grooming' => $grooming,
            ]
        );
    }
}

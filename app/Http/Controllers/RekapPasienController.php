<?php

namespace App\Http\Controllers;

use App\Models\Modeler;
use App\Models\Pasien;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class RekapPasienController extends Controller
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
        $kamar = $this->model->kategoriKamar()
            ->with([
                'KamarRawatInapDanBedah' => function ($q) {
                    $q->where('branch_id', Auth::user()->branch_id);
                    $q->withCount(['KamarRawatInapDanBedahDetail as jumlah' => function ($q) {
                        $q->select(DB::raw('count(kamar_rawat_inap_dan_bedah_id)'));
                        $q->where('status', 'In Use');
                    }]);
                }
            ])
            ->get();
        $pasien = $this->model->rekamMedisPasien()
            ->orderBy('created_at', 'ASC')
            ->whereHas('KamarRawatInapDanBedahDetail', function ($q) {
                $q->where('status', 'In Use');
            })
            ->where(function ($q) {
                if (!Auth::user()->akses('global')) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->has('Pendaftaran')
            ->get();

        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('id', 1)
            // ->where('dokter', me())
            ->first();

        $dokter = $this->model->user()
            ->whereHas('role', function ($q) {
                $q->where('type_role', 'DOKTER');
            })
            ->get();

        $hewan = $this->model->binatang()
            ->where('status', true)
            ->get();

        $owner = $this->model->owner()
            ->where('status', true)
            ->get();

        $pakan = $this->model->itemNonObat()
            ->where('jenis', 'PAKAN')
            ->with(['StockFirst' => function ($q) use ($data) {
                $q->where('branch_id', Auth::user()->branch_id);
            }])
            ->get();

        $itemNonObat = $this->model->itemNonObat()
            ->where('jenis', 'NON PAKAN')
            ->with(['StockFirst' => function ($q) use ($data) {
                $q->where('branch_id', Auth::user()->branch_id);
            }])
            ->get();

        $rekomendasiTindakanBedah = $this->model->tindakan()
            ->whereHas('poli', function ($q) {
                $q->where('name', 'Bedah');
            })
            ->where('status', true)
            ->get();

        return view('rawat_inap/rekap_pasien/rekap_pasien', compact('pasien', 'data', 'kamar', 'dokter', 'hewan', 'owner', 'pakan', 'itemNonObat', 'rekomendasiTindakanBedah'));
    }

    public function detail($id)
    {
        return view('quick_menu/pemeriksaan_pasien/pemeriksaan_pasien');
    }

    public function getListRuangan(Request $req)
    {
        $kamar = $this->model->kategoriKamar()
            ->with([
                'KamarRawatInapDanBedah' => function ($q) {
                    $q->withCount(['KamarRawatInapDanBedahDetail as jumlah' => function ($q) {
                        $q->select(DB::raw('count(kamar_rawat_inap_dan_bedah_id)'));
                        $q->where('status', 'In Use');
                    }]);
                }
            ])
            ->get();

        return view('rawat_inap/ruangan/list_ruangan', compact('kamar'));
    }

    public function datatable(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->where('status_pemeriksaan', 'Rawat Inap')
            ->where(function ($q) use ($req) {
                if ($req->tanggal_awal != '') {
                    $q->whereDate('created_at', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->whereDate('created_at', '<=', $req->tanggal_akhir);
                }

                $q->whereHas('KamarRawatInapDanBedahDetailFirst', function ($q) use ($req) {
                    $q->where('status', 'In Use');
                });

                if ($req->dokter_id != '') {
                    $q->where('created_by', $req->dokter_id);
                }

                if ($req->binatang_id != '') {
                    $q->whereHas('pasien', function ($q) use ($req) {
                        $q->where('binatang_id', $req->binatang_id);
                    });
                }

                if ($req->owner_id != '') {
                    $q->whereHas('pasien', function ($q) use ($req) {
                        $q->where('owner_id', $req->owner_id);
                    });
                }
            })
            ->get();

        return DataTables::of($data)
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
            ->addColumn('owner', function ($data) {
                return $data->pasien != null ? ($data->pasien != '' ? $data->pasien->owner->name : '-')  : "-";
            })
            ->addColumn('tanggal_masuk', function ($data) {
                return CarbonParse($data->created_at, 'd/m/Y');
            })
            ->addColumn('tanggal_keluar', function ($data) {

                if ($data->tanggal_keluar) {
                    return CarbonParse($data->tanggal_keluar, 'd/m/Y');
                }
                return '-';
            })
            ->addColumn('pasien', function ($data) {
                return $data->pasien != null ? $data->pasien->name  : "-";
            })
            ->addColumn('dokter', function ($data) {
                return $data->CreatedBy != null ? $data->CreatedBy->name  : "-";
            })
            ->addColumn('tindakan_medis', function ($data) {
                $html = '<table style="width:100%">';
                $html .= '<tr><td>Rawat Inap</td><td class="text-center" style="width:170px">' . ($data->rawat_inap ? '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Ya</span>' : '<span class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Tidak</span>') . '</td></tr>';
                $html .= '<tr><td>Status Urgent</td><td class="text-center">' . ($data->status_urgent ? '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Ya</span>' : '<span class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Tidak</span>') . '</td></tr>';

                if ($data->status_pengambilan_obat and $data->status_pembayaran) {
                    $html .= '<tr><td>Status Pasien</td><td class="text-center"><span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Sudah Pulang</span></td></tr>';
                } else {
                    $html .= '<tr><td>Status Pasien</td><td class="text-center"><span class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Sedang Pemeriksaan</span></td></tr>';
                }

                return $html;
            })
            ->addColumn('jumlah_data', function ($data) {
                $html = '<table style="width:100%">';
                $html .= '<tr><td>Catatan</td><td>' . ($data->rekamMedisCatatan->count() + 1) . '</td></tr>';
                $html .= '<tr><td>Obat</td><td>' . ($data->rekamMedisResep->count()) . '</td></tr>';
                $html .= '<tr><td>Tindakan</td><td>' . ($data->rekamMedisTindakan->count()) . '</td></tr>';
                $html .= '<tr><td>Pakan</td><td>' . ($data->rekamMedisPakan->count()) . '</td></tr>';
                $html .= '<tr><td>Bedah</td><td>' . ($data->RekamMedisRekomendasiTindakanBedah->where('status', 'Done')->count()) . '</td></tr>';
                $html .= '<tr><td>Diagnosa</td><td>' . ($data->rekamMedisDiagnosa->count() + 1) . '</td></tr>';
                $html .= '<tr><td>Hasil Lab</td><td>' . ($data->rekamMedisHasilLab->count()) . '</td></tr>';
                $html .= '<tr><td>Form Persetujuan</td><td>' . ($data->upload_form_persetujuan ? '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Sudah</span>' : '<span class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Belum</span>') . '</td></tr>';

                return $html;
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('image', function ($data) {
                return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->image . '" alt="No image">';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image', 'tindakan_medis', 'jumlah_data'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Pasien $req)
    {
        $tanggal = Carbon::now()->format('Ymd');
        $binatang = $this->model->binatang()->find($req->binatang_id);
        $kode = 'RM-' . $binatang->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->rekamMedisPasien()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->rekamMedisPasien()
            ->selectRaw('cast(substring(kode,' . $sub . ') as INTEGER ) as id')
            ->get();
        $count = (int)$index->id;
        $collect_id = [];
        for ($i = 0; $i < count($collect); $i++) {
            array_push($collect_id, (int)$collect[$i]->id);
        }

        $flag = 0;
        for ($i = 0; $i < $count; $i++) {
            if ($flag == 0) {
                if (!in_array($i + 1, $collect_id)) {
                    $index = $i + 1;
                    $flag = 1;
                }
            }
        }

        if ($flag == 0) {
            $index = (int)$index->id + 1;
        }

        $len = strlen($index);

        if ($len < 5) {
            $pad = 4;
        } else {
            $pad = $len;
        }

        $index = str_pad($index, $pad, '0', STR_PAD_LEFT);

        $kode = $kode . $index;
        return $kode;
    }

    public function tambahResep(Request $req)
    {
        $rekamMedis = $this->model->rekamMedisPasien()->find($req->id);


        $produkObat = $this->model->produkObat()
            ->with([
                'StockFirst' => function ($q) use ($rekamMedis) {
                    $q->where('branch_id', $rekamMedis->Pendaftaran->branch_id);
                }
            ])
            ->where('status', true)
            ->get();

        return view('rawat_inap/ruangan/template_resep', compact('req', 'produkObat'));
    }

    public function tambahRacikanChild(Request $req)
    {
        $rekamMedis = $this->model->rekamMedisPasien()->find($req->id);

        $produkObat = $this->model->produkObat()
            ->with([
                'StockFirst' => function ($q) use ($rekamMedis) {
                    $q->where('branch_id', $rekamMedis->Pendaftaran->branch_id);
                }
            ])
            ->where('status', true)
            ->get();
        return view('rawat_inap/ruangan/template_racikan_child', compact('req', 'produkObat'));
    }

    public function getRekamMedis(Request $req)
    {
        $data = $this->model->Pasien()
            ->find($req->id);

        $rm = $this->model->rekamMedisPasien()
            ->where('pasien_id', $data->id)
            ->where('status_pembayaran', false)
            ->where('status_pemeriksaan', 'Rawat Inap')
            ->first();

        return view('rawat_inap/ruangan/template_data', compact('data', 'rm'));
    }

    public function addRekamMedisLogHistory($id, $text, $table, $refId)
    {
        $idRekamMedisLogHistory = $this->model->rekamMedisLogHistory()
            ->where('rekam_medis_pasien_id', $id)
            ->max('id') + 1;

        $this->model->rekamMedisLogHistory()
            ->create([
                'rekam_medis_pasien_id' => $id,
                'id'    => $idRekamMedisLogHistory,
                'description'   => $text,
                'table'   => $table,
                'ref_id'   => $refId,
                'created_by'    =>  me(),
                'updated_by'    => me(),
            ]);
        return true;
    }

    public function generateKodeJurnal($branchKode)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode =  'JR-' . $branchKode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->jurnal()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->jurnal()
            ->selectRaw('cast(substring(kode,' . $sub . ') as INTEGER ) as id')
            ->get();
        $count = (int)$index->id;
        $collect_id = [];
        for ($i = 0; $i < count($collect); $i++) {
            array_push($collect_id, (int)$collect[$i]->id);
        }

        $flag = 0;
        for ($i = 0; $i < $count; $i++) {
            if ($flag == 0) {
                if (!in_array($i + 1, $collect_id)) {
                    $index = $i + 1;
                    $flag = 1;
                }
            }
        }

        if ($flag == 0) {
            $index = (int)$index->id + 1;
        }

        $len = strlen($index);

        if ($len < 5) {
            $pad = 4;
        } else {
            $pad = $len;
        }

        $index = str_pad($index, $pad, '0', STR_PAD_LEFT);

        $kode = $kode . $index;

        return Response()->json(['status' => 1, 'kode' => $kode]);
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->pasien()->where('id', $req->id)
                ->update([
                    'status' => $req->param
                ]);
            return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }

    public function edit(Request $req)
    {
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model->pasien()->with(['ras'])->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);

            switch ($req->param) {
                case 'log':
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    break;
                case 'diagnosa':

                    $this->model->rekamMedisDiagnosa()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_diagnosa')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                case 'treatment':
                    $this->model->rekamMedisTreatment()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_treatment')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                case 'tindakan':
                    $this->model->rekamMedisTindakan()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_tindakan')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                case 'resep':
                    $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();

                    $this->model->rekamMedisResepRacikan()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('rekam_medis_resep_id', $req->id_detail)
                        ->delete();

                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_resep')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                case 'hasil lab':
                    $data =  $this->model->rekamMedisHasilLab()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->first();

                    if (is_file($data->file)) {
                        unlink($data->file);
                    }

                    $this->model->rekamMedisHasilLab()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_hasil_lab')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                    break;
                default:
                    break;
            }

            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function select2(Request $req)
    {
        switch ($req->param) {
            case 'tindakan_id':
                $rekamMedis = $this->model->rekamMedisPasien()->find($req->id);
                return $this->model->tindakan()
                    ->select('id', DB::raw("name as text"), 'mk_tindakan.*')
                    ->where('binatang_id', $rekamMedis->pasien->binatang_id)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            case 'kamar_rawat_inap_dan_bedah_id':

                $checkKetersediaan = $this->model->kamarRawatInapDanBedah()
                    ->select('id', DB::raw("name as text"), 'mka_kamar_rawat_inap_dan_bedah.*')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->with(['Branch', 'KategoriKamar'])
                    ->get();
                $exclude = [0];

                $kamarTerpakai = $this->model->kamarRawatInapDanBedahDetail()
                    ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                    ->where('status', 'In Use')
                    ->first();

                foreach ($checkKetersediaan as $i => $d) {
                    if ($d->kapasitas <= $d->KamarRawatInapDanBedahDetail->where('status', 'In Use')->count()) {
                        array_push($exclude, $d->id);
                    }
                }

                return $this->model->kamarRawatInapDanBedah()
                    ->select('id', DB::raw("name as text"), 'mka_kamar_rawat_inap_dan_bedah.*')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->with(['Branch', 'KategoriKamar'])
                    ->whereDoesntHave('KamarRawatInapDanBedahDetail', function ($q) use ($kamarTerpakai) {
                        $q->where('kamar_rawat_inap_dan_bedah_id', $kamarTerpakai->kamar_rawat_inap_dan_bedah_id);
                    })
                    ->withCount(['kamarRawatInapDanBedahDetail as terpakai' => function ($q) {
                        $q->where('status', 'In Use');
                    }])
                    ->whereNotIn('id', $exclude)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->q) . '%');
                        $q->orWhereHas('KategoriKamar', function ($q) use ($req) {
                            $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->q) . '%');
                        });
                    })
                    ->paginate(10);
            default:
                # code...
                break;
        }
    }

    public function print(Request $req)
    {
        $data = $this->model->rekamMedisPasien()->findOrFail($req->id);
        $pdf = PDF::loadview('quick_menu/pemeriksaan_pasien/print_pemeriksaan_pasien', compact('data'))->setPaper('a4', 'potrait');
        return $pdf->stream('FORM PERSETUJUAN-' . $data->kode . '-' . carbon::now()->format('Y-m-d') . '.pdf');
    }
}

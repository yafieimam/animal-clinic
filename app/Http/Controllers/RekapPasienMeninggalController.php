<?php

namespace App\Http\Controllers;

use App\Exports\RekapPasienMeninggalExport;
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
use Maatwebsite\Excel\Facades\Excel;

class RekapPasienMeninggalController extends Controller
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

        $kamarRawatInapDanBedah = $this->model->kamarRawatInapDanBedah()
            ->get();
        return view('management_pasien/rekap_pasien_meninggal/rekap_pasien_meninggal', compact('pasien', 'data', 'kamar', 'dokter', 'hewan', 'owner', 'pakan', 'itemNonObat', 'rekomendasiTindakanBedah', 'kamarRawatInapDanBedah'));
    }

    public function datatable(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->whereHas('pasienMeninggal', function ($q) use ($req) {
                if ($req->tanggal_awal != '') {
                    $q->whereDate('created_at', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->whereDate('created_at', '<=', $req->tanggal_akhir);
                }
            })
            ->whereHas('Pendaftaran', function ($q) use ($req) {
                if ($req->dokter_poli != '') {
                    $q->where('dokter', $req->dokter_poli);
                }

                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->whereHas('kamarRawatInapDanBedahDetailFirst', function ($q) use ($req) {
                if ($req->kamar_rawat_inap_dan_bedah_id != '') {
                    $q->where('status_pindah', false);
                    $q->where('kamar_rawat_inap_dan_bedah_id', $req->kamar_rawat_inap_dan_bedah_id);
                }
            })
            ->with([
                'kamarRawatInapDanBedahDetailFirst' => function ($q) use ($req) {
                    $q->where('status_pindah', false);
                    if ($req->kamar_rawat_inap_dan_bedah_id != '') {
                        $q->where('kamar_rawat_inap_dan_bedah_id', $req->kamar_rawat_inap_dan_bedah_id);
                    }
                },
                'pasienMeninggal',
                'pasien',
            ])
            ->get();


        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('rawat_inap/ruangan/action_button_ruangan', compact('data'));
            })
            ->addColumn('name', function ($data) {
                return ($data->Pendaftaran->Pasien != '' ? $data->Pendaftaran->Pasien->name : '-');
            })
            ->addColumn('owner', function ($data) {
                return ($data->Pasien->Owner != '' ? $data->Pasien->Owner->name : '-');
            })
            ->addColumn('dokter_poli', function ($data) {
                return $data->Pendaftaran->Dokter->name;
            })
            ->addColumn('kamar_rawat_inap_dan_bedah', function ($data) {
                return $data->kamarRawatInapDanBedahDetailFirst->KamarRawatInapDanBedah->name;
            })
            ->addColumn('binatang', function ($data) {
                return $data->Pasien->binatang != null ? $data->Pasien->binatang->name  : "-";
            })
            ->addColumn('branch', function ($data) {
                return $data->Pendaftaran->Branch != null ? $data->Pendaftaran->Branch->kode  : "-";
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('image', function ($data) {
                return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->image . '" alt="No image">';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image'])
            ->addIndexColumn()
            ->make(true);
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

    public function rekapPasienMeninggalExcel(Request $req)
    {

        return Excel::download(new RekapPasienMeninggalExport($req), 'pasien_meninggal_list.xlsx');
    }
}

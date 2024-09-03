<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Image;
use Yajra\DataTables\Facades\DataTables;

class MonitoringAntrianObatController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('quick_menu/monitoring_antrian_obat/monitoring_antrian_obat');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
                $q->whereHas('rekamMedisResep', function ($q) {
                    $q->where('status_resep', 'Antrian');
                    $q->where('status_pembuatan_obat', 'Undone');
                });
            })
            ->where('status_pengambilan_obat', false)
            ->where('status_pembayaran', false)
            ->whereHas('pendaftaran', function ($q) {
                $q->where('status', 'Completed');
            })
            ->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap'])
            ->get();
        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<button class="btn btn-primary">Pilih</button>';
            })
            ->addColumn('kode_pendaftaran', function ($data) {
                return $data->Pendaftaran->kode_pendaftaran;
            })
            ->addColumn('nama_pasien', function ($data) {
                return $data->Pasien->name;
            })
            ->addColumn('nama_owner', function ($data) {
                return $data->Pasien->Owner->name;
            })
            ->addColumn('jenis_hewan', function ($data) {
                return $data->Pasien->Binatang->name;
            })
            ->addColumn('obat', function ($data) {
                $html = '<table style="width:100%">';

                foreach ($data->rekamMedisResep as $key => $value) {
                    if ($value->status_resep == 'Antrian') {
                        $html .= '<tr><td>' . ($value->ProdukObat ? $value->ProdukObat->name : ($value->KategoriObat ? $value->KategoriObat->name : 'Data Corrupt')) . '</td><td>' . ($value->description) . '</td></tr>';
                    }
                }

                $html .= '</table>';

                return $html;
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'obat'])
            ->addIndexColumn()
            ->make(true);
    }

    public function getPasien(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
                $q->whereHas('rekamMedisResep', function ($q) {
                    $q->where('status_resep', 'Antrian');
                    $q->where('status_pembuatan_obat', 'Undone');
                });
            })
            ->where('status_pengambilan_obat', false)
            ->where('status_pembayaran', false)
            ->whereHas('pendaftaran', function ($q) {
                $q->where('status', 'Completed');
            })
            ->with(['Pendaftaran', 'Pasien'])
            ->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap'])
            ->orderBy('updated_at', 'ASC')
            ->get();
        $antrian = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }

                $q->whereHas('rekamMedisResep', function ($q) {
                    $q->where('status_resep', 'Antrian');
                    $q->where('status_pembuatan_obat', 'Undone');
                });
            })
            ->where('status_pengambilan_obat', false)
            ->where('status_pembayaran', false)
            ->whereHas('pendaftaran', function ($q) {
                $q->where('status', 'Completed');
            })
            ->with(['Pendaftaran', 'Pasien'])
            ->orderBy('updated_at', 'ASC')
            ->first();

        $dalamPenanganan = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->where('status_pengambilan_obat', true)
            ->whereHas('pendaftaran', function ($q) {
                $q->where('status', 'Completed');
            })
            ->orderBy('updated_at', 'ASC')
            ->count();

        if ($antrian == null) {
            $antrian = $this->model->rekamMedisPasien()
                ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal'])
                ->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    }
                })
                ->where('status_pengambilan_obat', false)
                ->where('status_pembayaran', false)
                ->whereHas('pendaftaran', function ($q) {
                    $q->where('status', 'Completed');
                })
                ->orderBy('updated_at', 'ASC')
                ->first();
        }

        $sisaAntrian = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->count() - $dalamPenanganan;
        $totalAntrian = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->whereHas('pendaftaran', function ($q) {
                $q->where('status', 'Completed');
            })
            ->count();
        return Response()->json(['status' => 1, 'data' => $data, 'antrian' => $antrian, 'total' => $totalAntrian, 'sisa' => $sisaAntrian]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->pendaftaran()->find($req->id)->update(['status' => 'Cancel']);
            return Response()->json(['status' => 1, 'message' => 'Batalkan Antrian Sukses']);
        });
    }
}

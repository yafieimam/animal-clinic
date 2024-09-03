<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LaporanPendaftaranController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model = new Modeler();
    }

    public function index(Request $req)
    {
        Auth::user()->akses('view', null, true);
        $dokter = $this->model
            ->user()
            ->whereHas('role', function ($q) {
                $q->where('type_role', 'DOKTER');
            })
            ->get();

        $hewan = $this->model
            ->binatang()
            ->where('status', true)
            ->get();

        $owner = $this->model
            ->owner()
            ->where('status', true)
            ->get();

        $poli = $this->model
            ->poli()
            ->where('status', true)
            ->get();

        return view('laporan.laporan_pendaftaran.rekap_laporan_pendaftaran', compact('req', 'dokter', 'hewan', 'owner', 'poli'));
    }

    public function datatable(Request $req)
    {
        $data = $this->model
            ->pendaftaran()
            // ->where('tanggal', dateStore())
            ->where(function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    if ($req->branch_id != '') {
                        $q->where('branch_id', $req->branch_id);
                    }
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->tanggal_periksa_awal != '') {
                    $q->where('tanggal', '>=', $req->tanggal_periksa_awal);
                }

                if ($req->tanggal_periksa_akhir != '') {
                    $q->where('tanggal', '<=', $req->tanggal_periksa_akhir);
                }

                if ($req->jam_pickup != '') {
                    $q->where('tanggal', '<=', $req->jam_pickup);
                }

                if ($req->dokter_id != '') {
                    $q->where('dokter', $req->dokter_id);
                }

                if ($req->poli_id != '') {
                    $q->where('poli_id', $req->poli_id);
                }

                if ($req->binatang_id != '') {
                    $q->whereHas('PendaftaranPasien.pasien', function ($q) use ($req) {
                        $q->where('binatang_id', $req->binatang_id);
                    });
                }

                if ($req->owner_id != '') {
                    $q->whereHas('PendaftaranPasien.pasien', function ($q) use ($req) {
                        $q->where('owner_id', $req->owner_id);
                    });
                }
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        return DataTables::of($data)
            ->addColumn('pasien', function ($data) {
                $html = '';
                foreach ($data->PendaftaranPasien as $key => $value) {
                    $html .= ($value->pasien ? $value->pasien->name : 'Data Corrupt Dihapus') . '<br>';
                }
                return $html;
            })
            ->addColumn('owner', function ($data) {
                return $data->Owner != null ? $data->Owner->name : '-';
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->lokasi : '-';
            })
            ->addColumn('poli', function ($data) {
                return $data->Poli != null ? $data->Poli->name : '-';
            })
            ->addColumn('dokter_periksa', function ($data) {
                $dokterperiksa = '';
                foreach ($data->PendaftaranPasien as $key => $value) {
                    $dokterperiksa .= ($value->Dokter != null ? $value->Dokter->name : '-') . '<br>';
                }
                // return $data->Dokter != null ? $data->Dokter->name  : "Batal di Pendaftaran";
                return $dokterperiksa;
            })
            ->addColumn('lain_lain', function ($data) {
                $tujuan = '';
                foreach ($data->PendaftaranPasien as $i => $d) {
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
            ->addColumn('updated_at', function ($data) {
                if ($data->jam_pickup == null) {
                    return '<div class="bg-white rounded p-2" style="text-align: center;>' . '<div class="flex justify-between"><center><b>' . '-' . '</b></center>' . '</div>' . '</div>';
                } else {
                    $jam_daftar_keluar = '<div class="bg-white rounded p-2">';

                    foreach ($data->PendaftaranPasien as $key => $d) {
                        $jam_daftar_keluar .= '<div class="flex justify-between"><b>' . CarbonParse($d->updated_at, 'd-M-Y H:i A') . '</b>' . '</div>';
                    }

                    $jam_daftar_keluar .= '</div>';
                }
                return $jam_daftar_keluar;
            })
            ->addColumn('status', function ($data) {
                $status = '';
                foreach ($data->PendaftaranPasien as $key => $d) {
                    
                    if ($d->status == "Sudah Diperiksa") {
                        $status .= '<div class="py-1 px-2 rounded-full text-xs bg-warning text-center text-white cursor-pointer font-medium">Selesai Diperiksa</div>' . '<br>';
                    } elseif ($d->status == "Belum Diperiksa") {
                        $status .= '<div class="py-1 px-2 rounded-full text-xs bg-warning text-center text-white cursor-pointer font-medium">Belum Diperiksa</div>' . '<br>';
                    } else {
                        $status .= '<div class="py-1 px-2 rounded-full text-xs bg-danger text-center text-white cursor-pointer font-medium">Batal Diperiksa</div>' . '<br>';
                    }
                }
                return $status;
            })
            ->addColumn('created_at', function ($data) {
                $html = '<div class="bg-white rounded p-2">';

                foreach ($data->PendaftaranPasien as $key => $d) {
                    $html .= '<div class="flex justify-between"><b>' . CarbonParse($d->created_at, 'd-M-Y H:i A') . '</b>' . '</div>';
                }

                $html .= '</div>';
                return $html;
            })
            ->addColumn('jam_pickup', function ($data) {
                if ($data->jam_pickup == null) {
                    return '<div class="bg-white rounded p-2" style="text-align: center;>' . '<div class="flex justify-between"><center><b>' . '-' . '</b></center>' . '</div>' . '</div>';
                } else {
                    return '<div class="bg-white rounded p-2">' . '<div class="flex justify-between"><b>' . date('d-M-Y H:i A', strtotime($data->jam_pickup)) . '</b>' . '</div>' . '</div>';
                }
            })
            ->addColumn('updated_by', function ($data) {
                $batal = '';
                foreach ($data->PendaftaranPasien as $key => $value) {
                    if ($data->status == "Cancel")
                    $batal .= ($value->UpdatedBy != null ? $value->UpdatedBy->name : '-') . '<br>';
                    else
                    $batal .= '-' . '<br>';
                }
                // return $data->Dokter != null ? $data->Dokter->name  : "Batal di Pendaftaran";
                return $batal;
            })
            ->addColumn('created_by', function ($data) {
                $pendaftar = '';
                foreach ($data->PendaftaranPasien as $key => $value) {
                    $pendaftar .= ($value->CreatedBy != null ? $value->CreatedBy->name : '-');
                }
                // return $data->Dokter != null ? $data->Dokter->name  : "pendaftar di Pendaftaran";
                return $pendaftar;
            })
            
            ->rawColumns(['sequence', 'pasien', 'status_owner', 'lain_lain', 'status', 'created_at', 'updated_at', 'dokter_periksa', 'branch', 'jam_pickup','updated_by'])
            ->addIndexColumn()
            ->make(true);
    }
}

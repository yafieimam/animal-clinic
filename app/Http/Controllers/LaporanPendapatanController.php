<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LaporanPendapatanController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index(Request $req)
    {
        Auth::user()->akses('view', null, true);

        if ($req->tanggal_awal == '' or !isset($req->tanggal_awal)) {
            $tanggal_awal = carbon::now()->startOfMonth()->format('Y-m-d');
        } else {
            $tanggal_awal = $req->tanggal_awal;
        }

        if ($req->tanggal_akhir == '' or !isset($req->tanggal_akhir)) {
            $tanggal_akhir = carbon::now()->endOfMonth()->format('Y-m-d');
        } else {
            $tanggal_akhir = $req->tanggal_akhir;
        }

        $data = $this->model->jurnal()
            ->where(function ($q) use ($req, $tanggal_awal, $tanggal_akhir) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if (Auth::user()->Branch) {
                    # code...
                }
                $q->where('tanggal', '>=', $tanggal_awal);
                $q->where('tanggal', '<=', $tanggal_akhir);
            })
            ->where(function ($q) use ($req) {
                $q->where('description', 'NOT LIKE', '%PENGELUARAN Stok%');
                $q->where('description', 'NOT LIKE', '%PENGELUARAN STOCK%');
                $q->where('description', 'NOT LIKE', '%PENGELUARAN STOK%');
            })
            ->orderBy('tanggal','ASC')
            ->get();
        return view('laporan/laporan_pendapatan/laporan_pendapatan', compact('req', 'data', 'tanggal_awal', 'tanggal_akhir'));
    }
}

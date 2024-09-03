<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Branch;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StatistikPendapatanController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('laporan/statistik_pendapatan/statistik_pendapatan');
    }

    public function getDataHighChart(Request $req)
    {
        $item = ['PENDAPATAN', 'PENGELUARAN'];
        $type = ['DEBET', 'KREDIT'];
        $data = [];
        $omzet = [];
        $isEnablingLegend = true;

        if ($req->branch_id != '') {
            $branch = explode(',', $req->branch_id);
        } else {
            $branch = [];
        }

        foreach ($item as $i => $d) {
            $data[$i]['name'] = $d;
            $data[$i]['data'] = $this->model->jurnal()
                ->select(\DB::raw('extract(epoch from date(tanggal))* 1000 as x'), DB::raw("count(*) as y"))
                ->where(function ($q) use ($branch) {
                    if (count($branch) != 0) {
                        $q->whereIn('branch_id', $branch);
                    }
                })
                ->where('dk', '=', $type[$i])
                ->orderByRaw('date(tanggal) ASC')
                ->groupByRaw('date(tanggal)')
                ->get();

            $omzet[$i]['name'] = $d;
            $omzet[$i]['data'] = $this->model->jurnal()
                ->select(\DB::raw('extract(epoch from date(tanggal))* 1000 as x'), DB::raw("sum(nominal) as y"))
                ->where('dk', '=', $type[$i])
                ->where(function ($q) use ($branch) {
                    if (count($branch) != 0) {
                        $q->whereIn('branch_id', $branch);
                    }
                })
                ->orderByRaw('date(tanggal) ASC')
                ->groupByRaw('date(tanggal)')
                ->get();
        }

        $omzet = array_values($omzet);
        $data = array_values($data);


        return Response()->json(['status' => 1, 'data' => $data, 'omzet' => $omzet, 'enable' => $isEnablingLegend]);
    }

    public function getDataBarHighChart(Request $req)
    {
        $data = [];
        $tanggal = $req->tahun . '-' . $req->bulan . '-01';
        // $data['title']['text'] = "Bar Chart Perbandingan Pendapatan Setiap Branch Periode " . Carbon::parse($tanggal)->format('F');
        $data['title']['text'] = "Grafik Perbandingan Keuangan Amore Animal Klinik";
        $data['subtitle']['text'] = "";
        $data['chart']['type'] = "line";
        $cabang = $this->model->branch()
            ->where('status', true)
            ->orderBy('kode')
            ->get();
        $data['xAxis'] = [];
        $jenis = ['PENDAPATAN', 'PENGELUARAN', 'PEMASUKAN'];

        foreach ($cabang as $i => $d) {
            $data['xAxis']['categories'][$i] = $d->lokasi;
        }

        $data['xAxis']['title']['text'] = null;
        // $data['yAxis']['min'] = 0;
        $data['yAxis']['title']['text'] = 'Pendapatan';
        $data['yAxis']['title']['align'] = 'high';
        $data['yAxis']['labels']['overflow'] = 'justify';
        $data['tooltip']['valuePrefix'] = 'Rp. ';
        $data['plotOptions']['bar']['dataLabels']['enabled'] = true;
        $data['legend']['layout'] = 'horizontal';
        $data['legend']['align'] = 'center';
        $data['legend']['verticalAlign'] = 'bottom';
        $data['legend']['x'] = 0;
        $data['legend']['y'] = 0;
        $data['legend']['floating'] = false;
        $data['legend']['borderWidth'] = 1;
        $data['legend']['backgroundColor'] = '#FFFFFF';
        $data['legend']['shadow'] = true;
        $data['credits']['enabled'] = false;
        foreach ($jenis as $i => $d) {
            $data['series'][$i]['name'] = $d;
            foreach ($cabang as $i1 => $d1) {
                switch ($d) {
                    case 'PENDAPATAN':
                        $debet = (float)$this->model->jurnal()
                            ->select(DB::raw("sum(nominal) as y"))
                            ->where('branch_id', '=', $d1->id)
                            ->where('dk', '=', 'DEBET')
                            ->whereIn('jenis', ['KASIR', 'DEPOSIT'])
                            ->where('tanggal', '>=', carbon::parse($tanggal)->startOfMonth()->format('Y-m-d'))
                            ->where('tanggal', '<=', carbon::parse($tanggal)->endOfMonth()->format('Y-m-d'))
                            ->sum('nominal');

                        $kredit = (float)$this->model->jurnal()
                            ->select(DB::raw("sum(nominal) as y"))
                            ->where('branch_id', '=', $d1->id)
                            ->whereIn('jenis', ['BUKTI KAS KELUAR', 'DEPOSIT'])
                            ->where('dk', '=', 'KREDIT')
                            ->where('tanggal', '>=', carbon::parse($tanggal)->startOfMonth()->format('Y-m-d'))
                            ->where('tanggal', '<=', carbon::parse($tanggal)->endOfMonth()->format('Y-m-d'))
                            ->sum('nominal');

                        $data['series'][$i]['data'][$i1] = (float)($debet - $kredit);
                        break;
                    case 'PEMASUKAN':
                        $data['series'][$i]['data'][$i1] = (float)$this->model->jurnal()
                            ->select(DB::raw("sum(nominal) as y"))
                            ->where('branch_id', '=', $d1->id)
                            ->where('dk', '=', 'DEBET')
                            ->whereIn('jenis', ['KASIR', 'DEPOSIT'])
                            ->where('tanggal', '>=', carbon::parse($tanggal)->startOfMonth()->format('Y-m-d'))
                            ->where('tanggal', '<=', carbon::parse($tanggal)->endOfMonth()->format('Y-m-d'))
                            ->sum('nominal');
                        break;
                    case 'PENGELUARAN':
                        $data['series'][$i]['data'][$i1] = (float)$this->model->jurnal()
                            ->select(DB::raw("sum(nominal) as y"))
                            ->where('branch_id', '=', $d1->id)
                            ->whereIn('jenis', ['BUKTI KAS KELUAR', 'DEPOSIT'])
                            ->where('dk', '=', 'KREDIT')
                            ->where('tanggal', '>=', carbon::parse($tanggal)->startOfMonth()->format('Y-m-d'))
                            ->where('tanggal', '<=', carbon::parse($tanggal)->endOfMonth()->format('Y-m-d'))
                            ->sum('nominal');
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return response()->json($data);
    }

    public function getDataPieChart(Request $req)
    {
        $tanggal = $req->tahun . '-' . $req->bulan . '-01';
        $metodePembayaran['param'] = [
            'TUNAI',
            'DEBET',
            'TRANSFER',
        ];

        $temp = 0;

        foreach ($metodePembayaran['param'] as $i => $d) {
            $metodePembayaran['data'][$i] = [];
            $metodePembayaran['data'][$i]['name'] = $d;
            $metodePembayaran['data'][$i]['y'] = $this->model->kasir()
                ->where('metode_pembayaran', $d)
                ->where('tanggal', '>=', carbon::parse($tanggal)->startOfMonth()->format('Y-m-d'))
                ->where('tanggal', '<=', carbon::parse($tanggal)->endOfMonth()->format('Y-m-d'))
                ->count() * 1;

            if ($metodePembayaran['data'][$i]['y'] >= $temp) {
                if ($i > 0) {
                    foreach ($metodePembayaran['data'] as $i1 => $d1) {
                        $metodePembayaran['data'][$i1]['sliced'] = false;
                        $metodePembayaran['data'][$i1]['selected'] = false;
                    }
                }

                $metodePembayaran['data'][$i]['sliced'] = true;
                $metodePembayaran['data'][$i]['selected'] = true;
                $temp = $metodePembayaran['data'][$i]['y'];
            }
        }

        $pembayaran['param'] = [
            'LUNAS',
            'CICILAN',
        ];

        $status = [
            true,
            false,
        ];


        foreach ($pembayaran['param'] as $i => $d) {
            $pembayaran['data'][$i] = [];
            $pembayaran['data'][$i]['name'] = $d;

            $pembayaran['data'][$i]['y'] = $this->model->kasir()
                ->where('langsung_lunas', $status[$i])
                ->where('tanggal', '>=', carbon::parse($tanggal)->startOfMonth()->format('Y-m-d'))
                ->where('tanggal', '<=', carbon::parse($tanggal)->endOfMonth()->format('Y-m-d'))
                ->count() * 1;

            if ($pembayaran['data'][$i]['y'] >= $temp) {
                if ($i > 0) {
                    foreach ($pembayaran['data'] as $i1 => $d1) {
                        $pembayaran['data'][$i1]['sliced'] = false;
                        $pembayaran['data'][$i1]['selected'] = false;
                    }
                }

                $pembayaran['data'][$i]['sliced'] = true;
                $pembayaran['data'][$i]['selected'] = true;
                $temp = $pembayaran['data'][$i]['y'];
            }
        }

        $temp = 0;

        return Response()->json([
            'metode_pembayaran' => $metodePembayaran,
            'pembayaran' => $pembayaran,
        ]);
    }
}

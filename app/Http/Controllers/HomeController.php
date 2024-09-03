<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('pages/dashboard-overview-1');
    }

    public function searchMenu(Request $req)
    {
        $menu = Menu::where('status', true)
            ->where(function ($q) use ($req) {
                $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->param) . '%');

                $q->orWhere(DB::raw("UPPER(url)"), 'like', '%' . strtoupper($req->param) . '%');
                $q->orWhere(function ($q) use ($req) {
                    $q->whereHas('groupMenu', function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->param) . '%');
                    });
                });
            })
            ->take(5)
            ->get();

        return view('layout/components/result-menu', compact('menu'));
    }

    public function getDokter(Request $req)
    {
        try {
            $this->model->jadwalDokterDetail()
                ->whereHas('jadwalDokter', function ($q) {
                    $q->where('hari', '!=', convertDayToHari(Carbon::now()->format('l')));
                })
                ->update([
                    'status' => 'masuk'
                ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 1, 'message' => $th->getMessage()]);
        }

        $data = $this->model->jadwalDokterDetail()
        ->where(function ($q) use ($req) {
            $q->where('status', '=', null);
            $q->orWhere('status', '=', '');
            $q->orWhere('status', '=', 'cuti');
            $q->orWhere('status', '=', 'izin');
            $q->orWhere('status', '=', 'sakit');
            $q->orWhere('status', '=', 'masuk');
        })
            ->whereHas('jadwalDokter', function ($q) use ($req) {
                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
                $q->whereHas('JamPertama', function ($q) use ($req) {
                    $q->where(DB::raw("concat(jam_awal,':',menit_awal)"), '<=', Carbon::now()->format('H:i'));
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
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function getKamar(Request $req)
    {
        $checkKetersediaan = $this->model->kamarRawatInapDanBedah()
            ->select('id', DB::raw("name as text"), 'mka_kamar_rawat_inap_dan_bedah.*')
            ->where(function ($q) use ($req) {
                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->with(['Branch', 'KategoriKamar'])
            ->get();

        $exclude = [0];

        foreach ($checkKetersediaan as $i => $d) {
            if ($d->kapasitas <= $d->KamarRawatInapDanBedahDetail->where('status', 'In Use')->count()) {
                array_push($exclude, $d->id);
            }
        }

        $data =  $this->model->kamarRawatInapDanBedah()
            ->select('id', DB::raw("name as text"), 'mka_kamar_rawat_inap_dan_bedah.*')
            ->where(function ($q) use ($req) {
                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->with(['Branch', 'KategoriKamar'])
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
            ->get();

        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function columnPasienPerBulan(Request $req)
    {
        $branch = $this->model->branch()
            ->where('status', true)
            ->get();
        $tanggal = $req->tahun . '-' . $req->bulan . '-01';

        $data = [];
        $data['chart']['type'] = "column";
        $data['chart']['options3d']['enabled'] = true;
        $data['chart']['options3d']['alpha'] = 0;
        $data['chart']['options3d']['beta'] = 0;
        $data['chart']['options3d']['depth'] = 70;
        $data['chart']['options3d']['depth'] = 70;
        $data['title']['text'] = "Pasien Per Bulan Setiap Branch Periode " . carbon::parse($tanggal)->format('F Y');
        $data['subtitle']['text'] = "Klik kolom untuk memunculkan detail kolom";
        $data['xAxis']['type'] = 'category';
        $data['yAxis']['title']['text'] = "Total Pasien Per Bulan";
        $data['yAxis']['allowDecimals'] = false;
        $data['legend']['layout'] = 'horizontal';
        $data['legend']['align'] = 'center';
        $data['legend']['verticalAlign'] = 'bottom';
        $data['credits']['enabled'] = false;

        $data['plotOptions']['series']['depth'] = 25;
        $data['plotOptions']['series']['dataLabels']['enabled'] = true;
        $data['series'] = [];
        $data['series'][0]['name'] = 'Branch';
        $data['series'][0]['colorByPoint'] = true;
        $data['series'][0]['data'] = [];
        $drilldown = ['Total Pasien', 'Total Periksa', 'Total Bedah', 'Total Grooming', 'Total Steril', 'Total Emergency', 'Total Pasien Pick Up', 'Total Request Dokter'];

        $data['drilldown'] = [];
        $data['drilldown']['drillUpButton']['relativeTo'] = 'spacingBox';
        $data['drilldown']['drillUpButton']['position']['x'] = 0;
        $data['drilldown']['drillUpButton']['position']['y'] = 0;
        $data['drilldown']['breadcrumbs']['position']['align'] = 'right';
        $data['drilldown']['series'] = [];
        foreach ($branch as $key => $value) {
            $check = $this->model->pendaftaran()
                ->where('branch_id', $value->id)
                ->whereYear('tanggal', $req->tahun)
                ->whereMonth('tanggal', CarbonParse($tanggal, 'm'))
                ->count();
            $data['series'][0]['data'][$key]['name'] = $value->lokasi;
            $data['series'][0]['data'][$key]['y'] = $check;
            $data['series'][0]['data'][$key]['drilldown'] = $value->lokasi;

            $data['drilldown']['series'][$key]['name'] = $value->lokasi;
            $data['drilldown']['series'][$key]['id'] = $value->lokasi;
            $data['drilldown']['series'][$key]['data'] = [];
            foreach ($drilldown as $i => $d) {
                $name = $d;
                $y = 0;
                switch ($d) {
                    case 'Total Pasien':
                        $y = $this->model->pendaftaran()
                            ->where('branch_id', $value->id)
                            ->whereYear('tanggal', $req->tahun)
                            ->whereMonth('tanggal', CarbonParse($tanggal, 'm'))
                            ->count();
                        break;
                    case 'Total Periksa':
                        $y = $this->model->pendaftaran()
                            ->whereHas('Poli', function ($q) use ($req) {
                                $q->where('name', 'Periksa');
                            })
                            ->where('branch_id', $value->id)
                            ->whereYear('tanggal', $req->tahun)
                            ->whereMonth('tanggal', CarbonParse($tanggal, 'm'))
                            ->count();
                        break;
                    case 'Total Bedah':
                        $y = $this->model->rekamMedisPasien()
                            ->where('status_bedah', true)
                            ->whereHas('Pendaftaran', function ($q) use ($value) {
                                $q->where('branch_id', $value->id);
                            })
                            ->whereYear('created_at', $req->tahun)
                            ->whereMonth('created_at', CarbonParse($tanggal, 'm'))
                            ->count();
                        break;
                    case 'Total Grooming':
                        $y = $this->model->rekamMedisPasien()
                            ->whereHas('RekamMedisTindakan', function ($q) use ($req) {
                                $q->whereHas('Tindakan', function ($q) use ($req) {
                                    $q->where('name', 'like', 'Grooming%');
                                });
                            })
                            ->whereHas('pendaftaran', function ($q) use ($value) {
                                $q->where('branch_id', $value->id);
                            })
                            ->whereYear('created_at', $req->tahun)
                            ->whereMonth('created_at', CarbonParse($tanggal, 'm'))
                            ->count();
                        break;
                    case 'Total Steril':
                        $y = $this->model->pendaftaran()
                            ->whereHas('Poli', function ($q) use ($req) {
                                $q->where('name', 'Steril');
                            })
                            ->where('branch_id', $value->id)
                            ->whereYear('tanggal', $req->tahun)
                            ->whereMonth('tanggal', CarbonParse($tanggal, 'm'))
                            ->count();
                        break;
                    case 'Total Emergency':
                        $y = $this->model->pendaftaran()
                            ->whereHas('Poli', function ($q) use ($req) {
                                $q->where('name', 'Emergency');
                            })
                            ->where('branch_id', $value->id)
                            ->whereYear('tanggal', $req->tahun)
                            ->whereMonth('tanggal', CarbonParse($tanggal, 'm'))
                            ->count();
                        break;
                    case 'Total Pasien Pick Up':
                        $y = $this->model->pendaftaran()
                            ->where('status_pickup', true)
                            ->where('branch_id', $value->id)
                            ->whereYear('tanggal', $req->tahun)
                            ->whereMonth('tanggal', CarbonParse($tanggal, 'm'))
                            ->count();
                        break;
                    case 'Total Request Dokter':
                        $y = $this->model->pendaftaran()
                            ->has('requestDokter')
                            ->where('branch_id', $value->id)
                            ->whereYear('tanggal', $req->tahun)
                            ->whereMonth('tanggal', CarbonParse($tanggal, 'm'))
                            ->count();
                        break;
                    default:
                        break;
                }
                $data['drilldown']['series'][$key]['data'][$i]['name'] = $d;
                $data['drilldown']['series'][$key]['data'][$i]['y'] = $y;
            }
        }

        return response()->json($data);
    }

    public function trafficPasien(Request $req)
    {
        $branch = $this->model->branch()
            ->where('status', true)
            ->get();
        $tanggal = $req->tahun . '-' . $req->bulan . '-01';

        $data = [];
        $data['chart']['type'] = "spline";
        $data['chart']['plotBackgroundColor'] = null;
        $data['title']['text'] = "Pasien Harian Setiap Branch Periode " . carbon::parse($tanggal)->format('F Y');
        $data['subtitle']['text'] = "Banyak kolom data berdasarkan bulan yang dipilih";
        $data['xAxis']['type'] = 'datetime';
        $data['xAxis']['labels']['format'] = '{value:%d/%m/%y}';
        $data['yAxis']['title']['text'] = "Total Pasien Per Hari";
        $data['legend']['layout'] = 'horizontal';
        $data['legend']['align'] = 'center';
        $data['legend']['verticalAlign'] = 'bottom';
        $data['credits']['enabled'] = false;
        // $data['plotOptions']['series']['label']['connectorAllowed'] = true;
        $data['plotOptions']['spline']['marker']['enabled'] = true;
        // $data['plotOptions']['series']['pointStart'] = (int)carbon::parse($tanggal)->startOfMonth()->format('d');
        $data['plotOptions']['spline']['dashStyle'] = 'ShortDashDot';
        $data['series'] = [];
        foreach ($branch as $key => $value) {
            $data['series'][$key]['name'] = $value->lokasi;
            $data['series'][$key]['data'] = [];
            $date = [];
            for ($i = 0; $i < carbon::parse($tanggal)->endOfMonth()->format('d'); $i++) {
                $check = $this->model->pendaftaran_pasien()
                    ->where('status', 'Sudah Diperiksa')
                    ->whereHas('pendaftaran', function ($q) use ($value, $tanggal, $i) {
                        $q->where('tanggal', carbon::parse($tanggal)->startOfMonth()->subDays(-$i)->format('Y-m-d'));
                        $q->where('branch_id', $value->id);
                    })
                    ->count();

                if (carbon::parse($tanggal)->startOfMonth()->subDays(-$i)->format('Y-m-d') == '2022-11-07') {

                    if ($value->id == 4) {
                        // dd(carbon::parse($tanggal)->startOfMonth()->subDays(-$i)->timestamp);
                    }
                }
                array_push($date, [carbon::parse($tanggal)->startOfMonth()->subDays(- ($i + 1))->timestamp * 1000, $check]);
            }
            $data['series'][$key]['data'] = $date;
        }
        // $data['responsive']['rules'] = [];

        return response()->json($data);
    }

    function changeBranchOfDepositMutasi(Request $req): JsonResponse
    {
        try {
            $this->model->deposit_mutasi()
                ->where('deposit_id', $req->deposit_id)
                ->where('id', $req->id)
                ->update([
                    'branch_id' => $req->branch_id
                ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 1, 'message' => $th->getMessage()]);
        }

        return response()->json(['status' => 1]);
    }
}

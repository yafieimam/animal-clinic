<?php

namespace App\Http\Controllers;

use App\Models\Modeler;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanAdminHarianController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index(Request $req)
    {

        // $this->model->deposit_mutasi()
        //     ->whereNull('keterangan')
        //     ->update(['keterangan' => 'PENGAMBILAN DEPOSIT']);
        // if (Auth::user()->akses('global')) {
        if (!isset($req->tanggal_awal)) {
            $req->tanggal_awal = Carbon::now();
        }

        if (!isset($req->tanggal_akhir)) {
            $req->tanggal_akhir = Carbon::now();
        }

        if (!isset($req->branch_id)) {
            $req->branch_id = '';
        }

        if ($req->branch_id == 'undefined') {
            $req->branch_id = '';
        }

        $branch = $this->model->branch()
            ->where(function ($q) use ($req) {
                if (!Auth::user()->akses('global')) {
                    $q->where('id', Auth::user()->branch_id);
                }
                if ($req->branch_id != '') {
                    $q->where('id', $req->branch_id);
                }
            })
            ->with([
                'kasir' => function ($q) use ($req) {
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                    $q->withCount(
                        [
                            'KasirPembayaran as cash' => function ($q) use ($req) {
                                $q->select(DB::raw("coalesce(sum(nilai_pembayaran),0)"));
                                $q->where('jenis_pembayaran', 'TUNAI');
                                if ($req->tanggal_awal != '') {
                                    $q->whereDate('t_kasir_pembayaran.created_at', '>=', $req->tanggal_awal);
                                }

                                if ($req->tanggal_akhir != '') {
                                    $q->whereDate('t_kasir_pembayaran.created_at', '<=', $req->tanggal_akhir);
                                }
                            },
                            'KasirPembayaran as debet' => function ($q) use ($req) {
                                $q->select(DB::raw("coalesce(sum(nilai_pembayaran),0)"));
                                $q->where('jenis_pembayaran', 'DEBET');
                                if ($req->tanggal_awal != '') {
                                    $q->whereDate('t_kasir_pembayaran.created_at', '>=', $req->tanggal_awal);
                                }

                                if ($req->tanggal_akhir != '') {
                                    $q->whereDate('t_kasir_pembayaran.created_at', '<=', $req->tanggal_akhir);
                                }
                            },
                            'KasirPembayaran as transfer' => function ($q) use ($req) {
                                $q->select(DB::raw("coalesce(sum(nilai_pembayaran),0)"));
                                $q->where('jenis_pembayaran', 'TRANSFER');
                                if ($req->tanggal_awal != '') {
                                    $q->whereDate('t_kasir_pembayaran.created_at', '>=', $req->tanggal_awal);
                                }

                                if ($req->tanggal_akhir != '') {
                                    $q->whereDate('t_kasir_pembayaran.created_at', '<=', $req->tanggal_akhir);
                                }
                            },
                        ]
                    );
                },
            ])
            ->withCount([
                'depositMutasi as cash_deposit_debet' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nilai),0)"));
                    $q->where('metode_pembayaran', 'TUNAI');
                    $q->where('jenis_deposit', 'DEBET');
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    } else {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    }
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '>=', $req->tanggal_awal);
                    }

                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '<=', $req->tanggal_akhir);
                    }
                },
                'depositMutasi as debet_deposit_debet' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nilai),0)"));
                    $q->where('metode_pembayaran', 'DEBET');
                    $q->where('jenis_deposit', 'DEBET');
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    } else {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    }
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '>=', $req->tanggal_awal);
                    }

                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '<=', $req->tanggal_akhir);
                    }
                },
                'depositMutasi as transfer_deposit_debet' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nilai),0)"));
                    $q->where('metode_pembayaran', 'TRANSFER');
                    $q->where('jenis_deposit', 'DEBET');
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    } else {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    }
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '>=', $req->tanggal_awal);
                    }

                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '<=', $req->tanggal_akhir);
                    }
                },
                'depositMutasi as cash_deposit_kredit' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nilai),0)"));
                    $q->where('metode_pembayaran', 'TUNAI');
                    $q->where('jenis_deposit', 'KREDIT');
                    // $q->where('ref', 'not like', 'INV%');
                    $q->where('t_deposit_mutasi.keterangan', 'not like', 'PEMBAYARAN MENGGUNAKAN DEPOSIT%');
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    } else {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    }
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '>=', $req->tanggal_awal);
                    }

                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '<=', $req->tanggal_akhir);
                    }
                },
                'depositMutasi as debet_deposit_kredit' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nilai),0)"));
                    $q->where('metode_pembayaran', 'DEBET');
                    $q->where('status', 'Done');
                    // $q->where('ref', 'not like', 'INV%');
                    $q->where('jenis_deposit', 'KREDIT');
                    $q->where('t_deposit_mutasi.keterangan', 'not like', 'PEMBAYARAN MENGGUNAKAN DEPOSIT%');
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    } else {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    }
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '>=', $req->tanggal_awal);
                    }

                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '<=', $req->tanggal_akhir);
                    }
                },
                'depositMutasi as transfer_deposit_kredit' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nilai),0)"));
                    $q->where('metode_pembayaran', 'TRANSFER');
                    $q->where('jenis_deposit', 'KREDIT');
                    $q->where('status', 'Done');
                    // $q->where('ref', 'not like', 'INV%');
                    $q->where('t_deposit_mutasi.keterangan', 'not like', 'PEMBAYARAN MENGGUNAKAN DEPOSIT%');
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    } else {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    }
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '>=', $req->tanggal_awal);
                    }

                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_deposit_mutasi.updated_at', '<=', $req->tanggal_akhir);
                    }
                },
            ])
            ->withCount([
                'jurnal as pengeluaran_tunai' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nominal),0)"));
                    $q->whereIn('jenis', ['BUKTI KAS KELUAR']);
                    $q->where('dk', '=', 'KREDIT');
                    $q->where('metode_pembayaran', '=', 'TUNAI');
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_jurnal.tanggal', '>=', $req->tanggal_awal);
                    }
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_jurnal.tanggal', '<=', $req->tanggal_akhir);
                    }
                },
                'jurnal as pengeluaran_debet' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nominal),0)"));
                    $q->whereIn('jenis', ['BUKTI KAS KELUAR']);
                    $q->where('dk', '=', 'KREDIT');
                    $q->where('metode_pembayaran', '=', 'DEBET');
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_jurnal.tanggal', '>=', $req->tanggal_awal);
                    }
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_jurnal.tanggal', '<=', $req->tanggal_akhir);
                    }
                },
                'jurnal as pengeluaran_transfer' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nominal),0)"));
                    $q->whereIn('jenis', ['BUKTI KAS KELUAR']);
                    $q->where('dk', '=', 'KREDIT');
                    $q->where('metode_pembayaran', '=', 'TRANSFER');
                    if ($req->tanggal_awal != '') {
                        $q->whereDate('t_jurnal.tanggal', '>=', $req->tanggal_awal);
                    }
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                    if ($req->tanggal_akhir != '') {
                        $q->whereDate('t_jurnal.tanggal', '<=', $req->tanggal_akhir);
                    }
                },
            ])
            // ->with([
            //     'depositMutasi' => function ($q) use ($req) {
            //         $q->where('metode_pembayaran', 'TRANSFER');
            //         $q->where('jenis_deposit', 'KREDIT');
            //         // $q->where('ref', 'not like', 'INV%');
            //         $q->where('t_deposit_mutasi.keterangan', 'not like', 'PEMBAYARAN MENGGUNAKAN DEPOSIT%');

            //         if ($req->tanggal_awal != '') {
            //             $q->whereDate('t_deposit_mutasi.updated_at', '>=', $req->tanggal_awal);
            //         }

            //         if ($req->tanggal_akhir != '') {
            //             $q->whereDate('t_deposit_mutasi.updated_at', '<=', $req->tanggal_akhir);
            //         }
            //     }
            // ])
            ->get();


        // return $branch[0]->depositMutasi;

        $data = $this->model->jurnal()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->tanggal_awal != '') {
                    $q->where('tanggal', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('tanggal', '<=', $req->tanggal_akhir);
                }
            })
            ->where(function ($q) use ($req) {
                $q->where(function ($q) use ($req) {
                    $q->whereIn('jenis', ['KASIR', 'DEPOSIT']);
                    $q->where('dk', '=', 'DEBET');
                });
                $q->orWhere(function ($q) use ($req) {
                    $q->whereIn('jenis', ['BUKTI KAS KELUAR', 'DEPOSIT']);
                    $q->where('dk', '=', 'KREDIT');
                });
            })
            ->get();
        // return $data;
        $period = CarbonPeriod::create($req->tanggal_awal, $req->tanggal_akhir);

        // Iterate over the period
        $dates = [];
        foreach ($period as $i => $date) {
            $dates[$i]['tanggal'] = $date->format('Y-m-d');

            $dates[$i]['penerimaan'] = $this->model->jurnal()
                ->where(function ($q) use ($req) {
                    if (Auth::user()->akses('global')) {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                })
                ->where('tanggal', $date->format('Y-m-d'))
                ->where(function ($q) use ($req) {
                    $q->where(function ($q) use ($req) {
                        $q->whereIn('jenis', ['KASIR', 'DEPOSIT']);
                        $q->where('dk', '=', 'DEBET');
                    });
                })
                ->sum('nominal');

            $dates[$i]['pengeluaran'] = $this->model->jurnal()
                ->where(function ($q) use ($req) {
                    if (Auth::user()->akses('global')) {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                })
                ->where('tanggal', $date->format('Y-m-d'))
                ->where(function ($q) use ($req) {
                    $q->where(function ($q) use ($req) {
                        $q->whereIn('jenis', ['BUKTI KAS KELUAR', 'DEPOSIT']);
                        $q->where('dk', '=', 'KREDIT');
                    });
                })
                ->sum('nominal');
        }
        return view('transaksi/laporan_admin_harian/rekap_laporan_harian', compact('branch', 'req', 'data', 'dates'));
        // } else {
        //     return view('transaksi/laporan_admin_harian/laporan_admin_harian');
        // }
    }

    public function appendData(Request $req)
    {
        $tunai = $this->model->KasirPembayaran()
            ->whereHas('kasir', function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    $q->where('branch_id', $req->branch_id);
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->tanggal_awal != '') {
                    $q->where('tanggal', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('tanggal', '<=', $req->tanggal_akhir);
                }
            })
            ->where('jenis_pembayaran', 'TUNAI')
            ->sum('nilai_pembayaran');

        $debet = $this->model->KasirPembayaran()
            ->whereHas('kasir', function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    $q->where('branch_id', $req->branch_id);
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->tanggal_awal != '') {
                    $q->where('tanggal', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('tanggal', '<=', $req->tanggal_akhir);
                }
            })
            ->where('jenis_pembayaran', 'DEBET')
            ->sum('nilai_pembayaran');

        $transfer = $this->model->KasirPembayaran()
            ->whereHas('kasir', function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    $q->where('branch_id', $req->branch_id);
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->tanggal_awal != '') {
                    $q->where('tanggal', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('tanggal', '<=', $req->tanggal_akhir);
                }
            })
            ->where('jenis_pembayaran', 'TRANSFER')
            ->sum('nilai_pembayaran');

        $deposit = $this->model->KasirPembayaran()
            ->whereHas('kasir', function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    $q->where('branch_id', $req->branch_id);
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->tanggal_awal != '') {
                    $q->where('tanggal', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('tanggal', '<=', $req->tanggal_akhir);
                }
            })
            ->where('jenis_pembayaran', 'DEPOSIT')
            ->sum('nilai_pembayaran');

        $branch = $this->model->branch()
            ->where(function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    $q->where('id', $req->branch_id);
                } else {
                    $q->where('id', Auth::user()->branch_id);
                }
            })
            ->first();

        $data = $this->model->jurnal()
            ->where(function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    $q->where('branch_id', $req->branch_id);
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->tanggal_awal != '') {
                    $q->where('tanggal', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('tanggal', '<=', $req->tanggal_akhir);
                }
            })
            ->where(function ($q) use ($req) {
                $q->where(function ($q) use ($req) {
                    $q->whereIn('jenis', ['KASIR', 'DEPOSIT', 'BUKTI KAS KELUAR']);
                    $q->where('dk', '=', 'DEBET');
                });
                // $q->orWhere(function ($q) use ($req) {
                //     $q->whereIn('jenis', ['BUKTI KAS KELUAR']);
                // });
            })
            ->get();

        $period = CarbonPeriod::create($req->tanggal_awal, $req->tanggal_akhir);

        // Iterate over the period
        $dates = [];
        foreach ($period as $i => $date) {
            $dates[$i]['tanggal'] = $date->format('Y-m-d');

            $dates[$i]['penerimaan'] = $this->model->jurnal()
                ->where(function ($q) use ($req) {
                    if (Auth::user()->akses('global')) {
                        $q->where('branch_id', $req->branch_id);
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                })
                ->where('tanggal', $date->format('Y-m-d'))
                ->where(function ($q) use ($req) {
                    $q->where(function ($q) use ($req) {
                        $q->whereIn('jenis', ['KASIR', 'DEPOSIT']);
                        $q->where('dk', '=', 'DEBET');
                    });
                })
                ->sum('nominal');

            $dates[$i]['pengeluaran'] = $this->model->jurnal()
                ->where(function ($q) use ($req) {
                    if (Auth::user()->akses('global')) {
                        $q->where('branch_id', $req->branch_id);
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                })
                ->where('tanggal', $date->format('Y-m-d'))
                ->where(function ($q) use ($req) {
                    $q->where(function ($q) use ($req) {
                        $q->whereIn('jenis', ['BUKTI KAS KELUAR', 'DEPOSIT']);
                    });
                })
                ->sum('nominal');
        }

        return view('transaksi/laporan_admin_harian/data_laporan_admin_harian', compact('tunai', 'debet', 'transfer', 'req', 'branch', 'deposit', 'data', 'dates'));
    }
}

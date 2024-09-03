<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Mail\SendInvoice;
use App\Models\Modeler;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Image;
use Yajra\DataTables\Facades\DataTables;

class PembayaranController extends Controller
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
        diskonToFalseProdukObat();
        $pasien = $this->model->owner()
            ->whereHas('singleRekamMedisPasien', function ($q) {
                $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                $q->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                });
                $q->where('status_pengambilan_obat', true);
                $q->where('status_pembayaran', false);
                $q->where('rawat_inap', false);
                // $q->whereHas('pendaftaran', function ($q) {
                //     $q->where('status', 'Completed');
                // });
                $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
            })
            ->with(['singleRekamMedisPasien' => function ($q) {
                $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                $q->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                });
                $q->where('status_pengambilan_obat', true);
                $q->where('status_pembayaran', false);
                $q->where('rawat_inap', false);
                // $q->whereHas('pendaftaran', function ($q) {
                //     $q->where('status', 'Completed');
                // });
                $q->orderBy('updated_at', 'DESC');
                $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
            }])
            ->get();
        $pasien = $pasien->sortBy(function ($data, $key) {
            return $data->singleRekamMedisPasien->updated_at;
        });

        $pasienRawatInap = $this->model->owner()
            ->whereHas('singleRekamMedisPasien', function ($q) {
                $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                $q->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                });
                $q->where('status_pengambilan_obat', true);
                $q->where('status_pembayaran', false);
                $q->where('rawat_inap', true);
                // $q->whereHas('pendaftaran', function ($q) {
                //     $q->where('status', 'Completed');
                // });
                $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
            })
            ->with(['singleRekamMedisPasien' => function ($q) {
                $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                $q->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                });
                $q->where('status_pengambilan_obat', true);
                $q->where('status_pembayaran', false);
                $q->where('rawat_inap', true);
                // $q->whereHas('pendaftaran', function ($q) {
                //     $q->where('status', 'Completed');
                // });
                $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
            }])
            ->get();

        $pasienRawatInap = $pasienRawatInap->sortBy(function ($data, $key) {
            return $data->singleRekamMedisPasien->updated_at;
        });

        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('id', 1)
            // ->where('dokter', me())
            ->first();

        generateStock();
        return view('transaksi/pembayaran/pembayaran', compact('pasien', 'data', 'pasienRawatInap'));
    }

    public function history()
    {
        return view('transaksi/pembayaran/history_kasir');
    }

    public function datatableDeposit(Request $req)
    {
        if (!is_numeric($req->owner_id)) {
            $req->owner_id = 0;
        }

        $data = $this->model->deposit()
            ->where('owner_id', $req->owner_id)
            ->where('sisa_deposit', '!=', 0)
            ->where(function ($q) use ($req) {
                if ($req->owner_id != '') {
                    $q->where('owner_id', $req->owner_id);
                }
            })
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<button class="btn btn-primary" onclick="pilihDeposit(\'' . $data->id . '\')">Pilih</button>';
            })
            ->addColumn('icon', function ($data) {
                return '<i class="' . $data->icon . ' text-2xl"></i>';
            })
            ->addColumn('nilai_deposit', function ($data) {
                return number_format($data->nilai_deposit);
            })
            ->addColumn('sisa_deposit', function ($data) {
                return number_format($data->sisa_deposit);
            })
            ->addColumn('owner', function ($data) {
                return $data->owner ? $data->owner->name : '';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence'])
            ->addIndexColumn()
            ->make(true);
    }

    public function datatable(Request $req)
    {
        $data = $this->model->kasir()
            ->where(function ($q) use ($req) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
                if ($req->tanggal_awal != '') {
                    $q->where('created_at', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('created_at', '<=', $req->tanggal_akhir);
                }
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<button  onclick="printCheckout(\'' . $data->id . '\')"class="btn btn-primary" >Print</button>';
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('tanggal_buat', function ($data) {
                return $data->tanggal;
            })
            ->addColumn('metode_pembayaran', function ($data) {
                if ($data->metode_pembayaran == 'TUNAI') {
                    return '<span class="badge badge-info">TUNAI</span>';
                } else {
                    return '<span class="badge badge-primary">NON TUNAI</span>';
                }
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'metode_pembayaran'])
            ->addIndexColumn()
            ->make(true);
    }

    public function pilihDeposit(Request $req)
    {
        $data = $this->model->deposit()->find($req->id);

        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function generateOnQueue()
    {
        $pasien = $this->model->rekamMedisPasien()
            ->where('status_pemeriksaan', 'Boleh Pulang')
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
            ->get();

        return Response()->json(['status' => 1, 'message' => 'Berhasil generate data', 'data' => $pasien]);
    }

    public function getPembayaran(Request $req)
    {
        $data = $this->model->owner()->find($req->id);
        if ($data) {
            if ($req->jenis == 'Rawat Jalan') {
                $data = $this->model->owner()
                    ->where('id', $req->id)
                    ->whereHas('pasien', function ($q) {
                        $q->whereHas('rekamMedisPasien', function ($q) {
                            $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                            $q->where(function ($q) {
                                if (!Auth::user()->akses('global', null, false)) {
                                    $q->whereHas('Pendaftaran', function ($q) {
                                        $q->where('branch_id', Auth::user()->branch_id);
                                    });
                                }
                            });
                            $q->where('status_pengambilan_obat', true);
                            $q->where('status_pembayaran', false);
                            $q->where('rawat_inap', false);
                            // $q->whereHas('pendaftaran', function ($q) {
                            //     $q->where('status', 'Completed');
                            // });
                            $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                        });
                    })
                    ->with(['pasien' => function ($q) {
                        $q->whereHas('rekamMedisPasien', function ($q) {
                            $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                            $q->where(function ($q) {
                                if (!Auth::user()->akses('global', null, false)) {
                                    $q->whereHas('Pendaftaran', function ($q) {
                                        $q->where('branch_id', Auth::user()->branch_id);
                                    });
                                }
                            });
                            $q->where('status_pengambilan_obat', true);
                            $q->where('status_pembayaran', false);
                            $q->where('rawat_inap', false);
                            // $q->whereHas('pendaftaran', function ($q) {
                            //     $q->where('status', 'Completed');
                            // });
                            $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                        });
                        $q->with(['rekamMedisPasien' => function ($q) {
                            $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                            $q->where(function ($q) {
                                if (!Auth::user()->akses('global', null, false)) {
                                    $q->whereHas('Pendaftaran', function ($q) {
                                        $q->where('branch_id', Auth::user()->branch_id);
                                    });
                                }
                            });
                            $q->where('status_pengambilan_obat', true);
                            $q->where('status_pembayaran', false);
                            $q->where('rawat_inap', false);
                            // $q->whereHas('pendaftaran', function ($q) {
                            //     $q->where('status', 'Completed');
                            // });
                            $q->with([
                                'rekamMedisRekomendasiTindakanBedah' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'tindakan_id', DB::raw("count(rekam_medis_pasien_id) as qty"), 'status');
                                    $q->groupBy('rekam_medis_pasien_id', 'tindakan_id', 'status');
                                },
                                'rekamMedisResep' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'produk_obat_id', DB::raw("sum(harga_jual) as harga_jual"), DB::raw("sum(qty) as qty"), 'jenis_obat');
                                    $q->where('jenis_obat', 'non-racikan');
                                    $q->groupBy('rekam_medis_pasien_id', 'produk_obat_id', 'jenis_obat');
                                },
                                'rekamMedisResepRacikan' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'kategori_obat_id', DB::raw("sum(harga_jual) as harga_jual"), DB::raw("count(rekam_medis_pasien_id) as qty"), 'jenis_obat');
                                    $q->where('jenis_obat', 'racikan');
                                    $q->groupBy('rekam_medis_pasien_id', 'kategori_obat_id', 'jenis_obat');
                                },
                                'rekamMedisPakan' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'item_non_obat_id', DB::raw("sum(jumlah) as jumlah"));
                                    $q->groupBy('rekam_medis_pasien_id', 'item_non_obat_id');
                                },
                                'rekamMedisTindakan' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'tindakan_id', DB::raw("count(rekam_medis_pasien_id) as qty"));
                                    $q->groupBy('rekam_medis_pasien_id', 'tindakan_id');
                                },
                                'rekamMedisNonObat' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'item_non_obat_id', DB::raw("sum(jumlah) as jumlah"));
                                    $q->groupBy('rekam_medis_pasien_id', 'item_non_obat_id');
                                },
                            ]);
                            $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                        }]);
                    }])
                    ->first();
            } else {
                $data = $this->model->owner()
                    ->where('id', $req->id)
                    ->whereHas('pasien', function ($q) {
                        $q->whereHas('rekamMedisPasien', function ($q) {
                            $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                            $q->where(function ($q) {
                                if (!Auth::user()->akses('global', null, false)) {
                                    $q->whereHas('Pendaftaran', function ($q) {
                                        $q->where('branch_id', Auth::user()->branch_id);
                                    });
                                }
                            });
                            $q->where('status_pengambilan_obat', true);
                            $q->where('status_pembayaran', false);
                            $q->where('rawat_inap', true);
                            // $q->whereHas('pendaftaran', function ($q) {
                            //     $q->where('status', 'Completed');
                            // });
                            $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                        });
                    })
                    ->with(['pasien' => function ($q) {
                        $q->whereHas('rekamMedisPasien', function ($q) {
                            $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                            $q->where(function ($q) {
                                if (!Auth::user()->akses('global', null, false)) {
                                    $q->whereHas('Pendaftaran', function ($q) {
                                        $q->where('branch_id', Auth::user()->branch_id);
                                    });
                                }
                            });
                            $q->where('status_pengambilan_obat', true);
                            $q->where('status_pembayaran', false);
                            $q->where('rawat_inap', true);
                            // $q->whereHas('pendaftaran', function ($q) {
                            //     $q->where('status', 'Completed');
                            // });
                            $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                        });
                        $q->with(['rekamMedisPasien' => function ($q) {
                            $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                            $q->where(function ($q) {
                                if (!Auth::user()->akses('global', null, false)) {
                                    $q->whereHas('Pendaftaran', function ($q) {
                                        $q->where('branch_id', Auth::user()->branch_id);
                                    });
                                }
                            });
                            $q->where('status_pengambilan_obat', true);
                            $q->where('status_pembayaran', false);
                            $q->where('rawat_inap', true);
                            // $q->whereHas('pendaftaran', function ($q) {
                            //     $q->where('status', 'Completed');
                            // });
                            $q->with([
                                'rekamMedisResep' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'produk_obat_id', DB::raw("sum(harga_jual) as harga_jual"), DB::raw("sum(qty) as qty"), 'jenis_obat');
                                    $q->where('jenis_obat', 'non-racikan');
                                    $q->groupBy('rekam_medis_pasien_id', 'produk_obat_id', 'jenis_obat');
                                },
                                'rekamMedisResepRacikan' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'kategori_obat_id', DB::raw("sum(harga_jual) as harga_jual"), DB::raw("count(rekam_medis_pasien_id) as qty"), 'jenis_obat');
                                    $q->where('jenis_obat', 'racikan');
                                    $q->groupBy('rekam_medis_pasien_id', 'kategori_obat_id', 'jenis_obat');
                                },
                                'rekamMedisRekomendasiTindakanBedah' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'tindakan_id', DB::raw("count(rekam_medis_pasien_id) as qty"), 'status');
                                    $q->groupBy('rekam_medis_pasien_id', 'tindakan_id', 'status');
                                },
                                'rekamMedisPakan' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'item_non_obat_id', DB::raw("sum(jumlah) as jumlah"));
                                    $q->groupBy('rekam_medis_pasien_id', 'item_non_obat_id');
                                },
                                'rekamMedisNonObat' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'item_non_obat_id', DB::raw("sum(jumlah) as jumlah"));
                                    $q->groupBy('rekam_medis_pasien_id', 'item_non_obat_id');
                                },
                                'rekamMedisTindakan' => function ($q) {
                                    $q->select('rekam_medis_pasien_id', 'tindakan_id', DB::raw("count(rekam_medis_pasien_id) as qty"));
                                    $q->groupBy('rekam_medis_pasien_id', 'tindakan_id');
                                },
                                'kamarRawatInapDanBedahDetail' => function ($q) {
                                    $q->orderBy('created_at', 'DESC');
                                }
                            ]);
                            $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                        }]);
                    }])
                    ->first();
            }
        }
        $dokter = [];
        if ($data) {
            foreach ($data->pasien as $key => $value) {
                foreach ($value->rekamMedisPasien as $key1 => $value1) {
                    array_push($dokter, $value1->createdBy->name);
                }
                // return ($value->rekamMedisPasien);
            }
        }

        $dokter = array_unique($dokter);
        $dokter = array_values($dokter);
        $pendaftaranId = [];
        $penjemputan = [];
        if ($data) {
            foreach ($data->pasien as $i => $d) {
                foreach ($d->rekamMedisPasien as $i1 => $d1) {
                    array_push($pendaftaranId, $d1->pendaftaran_id);
                }
            }

            $penjemputan = $this->model->pendaftaran()
                ->where('status_pickup', true)
                ->whereIn('id', $pendaftaranId)
                ->where('status_pembayaran_penjemputan', false)
                ->get();
        }

        $kode = $this->generateKode($req)->getData()->kode;

        return view('transaksi/pembayaran/template_data', compact('data', 'kode', 'penjemputan', 'dokter', 'req'));
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
        return view('management_obat/apotek/template_resep', compact('req', 'produkObat'));
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
        return view('management_obat/apotek/template_racikan_child', compact('req', 'produkObat'));
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('Ymd');
        $kode =  'INV-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->kasir()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->kasir()
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

    public function generateKodeJurnal($branchKode)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode =  'JR-' . $branchKode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        // $index = $this->model->jurnal()
        //     ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
        //     ->where('kode', 'like', $kode . '%')
        //     ->first();

        // $collect = $this->model->jurnal()
        //     ->selectRaw('cast(substring(kode,' . $sub . ') as INTEGER ) as id')
        //     ->get();
        // $count = (int)$index->id;
        // $collect_id = [];
        // for ($i = 0; $i < count($collect); $i++) {
        //     array_push($collect_id, (int)$collect[$i]->id);
        // }

        // $flag = 0;
        // for ($i = 0; $i < $count; $i++) {
        //     if ($flag == 0) {
        //         if (!in_array($i + 1, $collect_id)) {
        //             $index = $i + 1;
        //             $flag = 1;
        //         }
        //     }
        // }

        // if ($flag == 0) {
        //     $index = (int)$index->id + 1;
        // }

        // $len = strlen($index);

        // if ($len < 5) {
        //     $pad = 4;
        // } else {
        //     $pad = $len;
        // }

        // $index = str_pad($index, $pad, '0', STR_PAD_LEFT);

        // $kode = $kode . $index;

        
    
        $todo = new Todo();
        $todo->id = $id;
        $todo->title = $request->get('title');
        $todo->save();

        $kode = $kode . str_pad($newId, 4, '0', STR_PAD_LEFT);

        return Response()->json(['status' => 1, 'kode' => $kode]);
    }

    public function generateItem(Request $req)
    {
        $data = $this->model->rekamMedisPasien()->find($req->id);
        $itemNonObat =    $this->model->itemNonObat()
            ->select('id', 'name', DB::raw("cast('NON OBAT' as varchar(20))  AS type"), 'harga', 'kategori')
            ->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->param) . '%')
            ->with([
                'StockFirst' => function ($q) use ($data) {
                    $q->where('branch_id', $data ? $data->Pendaftaran->branch_id : Auth::user()->branch_id);
                }
            ])
            ->where('status', true)
            ->whereIn('kategori', ['DIJUAL BEBAS'])
            ->get();

        $item =    $this->model->produkObat()
            ->select('id', 'name', DB::raw("cast('OBAT' as varchar(20))  AS type"), 'harga')
            ->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->param) . '%')
            ->where(function ($q) use ($req) {
                if ($req->jenis == 'diskon') {
                    $q->where('diskon', 'true');
                }
            })
            ->with([
                'StockFirst' => function ($q) use ($data) {
                    $q->where('branch_id', $data ? $data->Pendaftaran->branch_id : Auth::user()->branch_id);
                }
            ])
            ->where('status', true)
            ->get();
        return view('transaksi/pembayaran/item_kasir', compact('item', 'itemNonObat'));
    }

    public function penyebut($nilai = null)
    {
        $_this = new self;
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $_this->penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = $_this->penyebut($nilai / 10) . " puluh" . $_this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $_this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $_this->penyebut($nilai / 100) . " ratus" . $_this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $_this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $_this->penyebut($nilai / 1000) . " ribu" . $_this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $_this->penyebut($nilai / 1000000) . " juta" . $_this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $_this->penyebut($nilai / 1000000000) . " milyar" . $_this->penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $_this->penyebut($nilai / 1000000000000) . " trilyun" . $_this->penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public function getTerbilang(Request $req)
    {
        $data = ucwords($this->penyebut($req->nilai));

        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function generateKodeOwner(Request $req)
    {
        $tanggal = Carbon::now()->format('dmY');
        $branch = Auth::user()->branch;
        $kode = 'AMORE-' . $branch->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->owner()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            // ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->owner()
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

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            // Simpan Resep
            try {
                DB::beginTransaction();
                // DB::statement('LOCK TABLE t_jurnal, t_kasir, mp_owner, t_kasir_pembayaran, t_deposit_mutasi, mp_rekam_medis_resep, t_kasir_detail IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');
                
                // DB::statement('LOCK TABLES t_jurnal WRITE');

                $id = $this->model->kasir()->max('id') + 1;
                $kode = $this->generateKode($req)->getData()->kode;
                if (is_numeric($req->owner_id)) {
                    $checkOwner = $this->model->owner()
                        ->find($req->owner_id);

                    if (!$checkOwner) {
                        $checkOwner = null;
                    }
                } else {
                    $checkOwner = null;
                }


                if ($checkOwner == null) {
                    $ownerId = $this->model->owner()->max('id') + 1;
                    $kodeOwner = $this->generateKodeOwner($req)->getData()->kode;
                    $this->model->owner()
                        ->create([
                            'id'    => $ownerId,
                            'kode'  => $kodeOwner,
                            'name'  => $req->owner_id,
                            'branch_id' => Auth::user()->branch_id,
                            'email' => $req->email,
                            'telpon'    => str_replace('_', '', $req->telpon),
                            'alamat'    => '-',
                            'status'    => true,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    $namaMember = $req->owner_id;
                    $req->owner_id = $ownerId;
                } else {
                    $namaMember = $checkOwner->name;
                }

                $sisaDeposit = (convertNumber($req->deposit) - convertNumber($req->pembayaran));
                $deposit = $this->model->deposit()
                    ->where('owner_id', $req->owner_id)
                    ->first();

                
                $this->model->kasir()
                    ->create([
                        'id'    => $id,
                        'kode'  => $kode,
                        'type_kasir'  => $req->type_kasir,
                        'owner_id'  => $req->owner_id != '-' ? $req->owner_id : null,
                        'branch_id' => Auth::user()->branch_id,
                        'tanggal'   => dateStore($req->tanggal),
                        'nama_owner'    => $namaMember,
                        'total_obat'    => convertNumber($req->total_obat),
                        'total_item_diskon'    => convertNumber($req->total_item_diskon),
                        'total_item_non_diskon'    => convertNumber($req->total_item_non_diskon),
                        'total_lain'    => convertNumber($req->total_lain),
                        'diskon_penyesuaian'    => convertNumber($req->diskon_penyesuaian),
                        'total_bayar'   => convertNumber($req->total_bayar),
                        'diskon'    => convertNumber($req->diskon),
                        'deposit'    => $sisaDeposit > 0 ? convertNumber($req->pembayaran) : convertNumber($req->deposit),
                        'pembayaran'    => convertNumber($req->pembayaran),
                        'diterima'  => convertNumber($req->diterima),
                        'uang_kembali'  => convertNumber($req->uang_kembali),
                        'metode_pembayaran' => $req->metode_pembayaran,
                        'nama_bank' => $req->metode_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                        'nomor_kartu' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                        'nomor_transaksi' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_transaksi : null,
                        'kode_deposit' => $req->kode_deposit,
                        'tarik_deposit' => $req->tarik_deposit,
                        'email' => $req->email,
                        'catatan_kasir' => $req->catatan_kasir,
                        'status'    => true,
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);

                $penarikanDeposit = 0;
                if ($req->kode_deposit) {
                    if ($deposit) {
                        // $idJurnal = $this->model->jurnal()->max('id') + 1;

                        $idDepositMutasi = $this->model->deposit_mutasi()->where('deposit_id', $deposit->id)->max('id') + 1;

                        $this->model->kasirPembayaran()
                            ->create([
                                'kasir_id'   => $id,
                                'id'     => 1,
                                'ref'    => $deposit->kode,
                                'nilai_pembayaran'   => $sisaDeposit > 0 ? convertNumber($req->pembayaran) : convertNumber($req->deposit),
                                'keterangan'     => 'PEMBAYARAN MENGGUNAKAN DEPOSIT',
                                'jenis_pembayaran' => 'DEPOSIT',
                                'created_by'     => me(),
                                'updated_by'     => me(),
                            ]);


                        $this->model->deposit_mutasi()
                            ->create([
                                'deposit_id'    => $deposit->id,
                                'id'    => $idDepositMutasi,
                                'ref'   => $kode,
                                'branch_id' => Auth::user()->branch_id,
                                'jenis_deposit' => 'KREDIT',
                                'nilai' => $sisaDeposit > 0 ? convertNumber($req->pembayaran) : convertNumber($req->deposit),
                                'metode_pembayaran' => $req->metode_pembayaran,
                                'nama_bank' => $req->metode_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                                'nomor_kartu' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                                'keterangan'    => 'PEMBAYARAN MENGGUNAKAN DEPOSIT',
                                'created_by'    => me(),
                                'updated_by'    => me(),
                            ]);

                        if ($req->tarik_deposit == 'YA') {
                            $penarikanDeposit = reCalcDeposit($deposit->id);
                            if ($penarikanDeposit > 0) {
                                $idDepositMutasi = $this->model->deposit_mutasi()->where('deposit_id', $deposit->id)->max('id') + 1;

                                // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);
                                $this->model->jurnal()
                                    ->create([
                                        // 'id'    => $idJurnal,
                                        // 'kode'  => $kodeJurnal,
                                        'kode'  => generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode,
                                        'branch_id' =>  branchDeposit($deposit->id),
                                        'tanggal'   => dateStore($req->tanggal),
                                        'ref'   => $kode,
                                        'ref_id'   => $idDepositMutasi,
                                        'metode_pembayaran'   => $req->metode_pembayaran,
                                        'nama_bank' => $req->jenis_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                                        'nomor_kartu' => $req->jenis_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                                        'jenis'   => 'DEPOSIT',
                                        'dk'    => 'KREDIT',
                                        'description'    => 'PENGELUARAN SISA DEPOSIT ATAS KODE DEPOSIT ' . $req->kode_deposit,
                                        'nominal'   => $penarikanDeposit,
                                        'created_by'    => me(),
                                        'updated_by'    => me(),
                                    ]);

                                $this->model->deposit_mutasi()
                                    ->create([
                                        'deposit_id'    => $deposit->id,
                                        'id'    => $idDepositMutasi,
                                        'branch_id' => Auth::user()->branch_id,
                                        'jenis_deposit' => 'KREDIT',
                                        'ref'   => $kode,
                                        'nilai' => $penarikanDeposit,
                                        'metode_pembayaran' => $req->metode_pembayaran,
                                        'nama_bank' => $req->metode_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                                        'nomor_kartu' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                                        'keterangan'    => 'PENGAMBILAN SISA DEPOSIT',
                                        'created_by'    => me(),
                                        'updated_by'    => me(),
                                    ]);
                            }
                        }

                        $this->model->deposit()
                            ->where('owner_id', $req->owner_id)
                            ->update([
                                'nilai_deposit' => reCalcDeposit($deposit->id),
                                'sisa_deposit' => reCalcDeposit($deposit->id),
                            ]);

                        // $this->model->jurnalDetail()
                        //     ->where('pasien_id', $data->Pasien->id)
                        //     ->where('status_deposit', 'Released')
                        //     ->update([
                        //         'status_deposit' => 'Done'
                        //     ]);
                    }
                }

                if (isset($req->table)) {
                    foreach ($req->table as $i => $d) {
                        if ($req->stock[$i] == 'YA') {
                            if ($req->jenis_stock[$i] == 'NON OBAT') {
                                $stock = decreasingStock($req->jenis_stock[$i], $req->ref[$i], Auth::user()->Branch->id, $req->qty[$i], $kode);
                                $produkObat = $this->model->itemNonObat()->find($req->ref[$i]);

                                if ($req->qty[$i] == 0) {
                                    DB::rollBack();
                                    // DB::statement('UNLOCK TABLES');
                                    return Response()->json(['status' => 2, 'message' => 'Qty untuk item ' . $produkObat->name . ' tidak boleh Nol']);
                                }

                                if ($stock->getData()->qty != 0) {
                                    DB::rollBack();
                                    // DB::statement('UNLOCK TABLES');
                                    return Response()->json(['status' => 3, 'message' => 'Sisa Stok untuk  '  . $produkObat->name . ' tidak sama dengan yang ada di database. merefresh ulang.']);
                                }
                            } else {
                                $check = $this->model->produkObat()
                                    ->find($req->ref[$i]);

                                $produkObat = $check;

                                if ($req->qty[$i] == 0) {
                                    DB::rollBack();
                                    // DB::statement('UNLOCK TABLES');
                                    return Response()->json(['status' => 2, 'message' => 'Qty untuk item ' . $produkObat->name . ' tidak boleh Nol']);
                                }

                                if (!$produkObat) {
                                    DB::rollBack();
                                    // DB::statement('UNLOCK TABLES');
                                    return Response()->json(['status' => 2, 'message' => 'Master Obat untuk ' . $produkObat->name . ' Tidak Ada.']);
                                }

                                $stock = decreasingStock($req->jenis_stock[$i], $produkObat->id, Auth::user()->Branch->id, $req->qty[$i], $kode);
                                if ($stock->getData()->qty != 0) {
                                    DB::rollBack();
                                    // DB::statement('UNLOCK TABLES');
                                    return Response()->json(['status' => 3, 'message' => 'Sisa Stok untuk  '  . $produkObat->name . ' tidak sama dengan yang ada di database. merefresh ulang.']);
                                }

                                if (isset($req->pasien_id)) {
                                    if ($req->pasien_id != null) {
                                        $rmTemp = $this->model->rekamMedisPasien()
                                            ->where('pasien_id', $req->pasien_id)
                                            ->orderBy('created_at', 'DESC')
                                            ->first();
                                        if (!$rmTemp) {
                                            DB::rollBack();
                                            // DB::statement('UNLOCK TABLES');
                                            return Response()->json(['status' => 2, 'message' => 'Hewan ini belum pernah periksa. Tidak bisa menggunakan transaksi langsung']);
                                        }

                                        $idRekamMedisResep = $this->model->rekamMedisResep()
                                            ->where('rekam_medis_pasien_id', $rmTemp->id)
                                            ->max('id') + 1;

                                        $this->model->rekamMedisResep()
                                            ->create([
                                                'rekam_medis_pasien_id' => $rmTemp->id,
                                                'id' => $idRekamMedisResep,
                                                'produk_obat_id' => $produkObat->id,
                                                'status_pembuatan_obat' => 'Done',
                                                'status_resep' => 'Kasir',
                                                'jenis_obat' => 'non-racikan',
                                                'qty' => $req->qty[$i],
                                                'harga_jual' => convertNumber($req->harga[$i]),
                                                'description' => '-',
                                                'created_by' => me(),
                                                'updated_by' => me(),
                                            ]);

                                        $text = 'Membeli obat <b>' . $produkObat->name . '</b> dikasir';
                                        $this->addRekamMedisLogHistory($rmTemp->id, $text, 'mp_rekam_medis_resep', $idRekamMedisResep);
                                    }
                                }
                            }

                            if (count($stock->getData()->mutasi) == 0) {
                                DB::rollBack();
                                // DB::statement('UNLOCK TABLES');
                                return Response()->json(['status' => 2, 'message' => 'Stok untuk  ' . $req->jenis_stock[$i] . ' ' . $produkObat->name . ' sudah habis.']);
                            }

                            // $idJurnal = $this->model->jurnal()->max('id') + 1;
                            // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);

                            $this->model->jurnal()
                                ->create([
                                    // 'id'    => $idJurnal,
                                    'kode'  => generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode,
                                    // 'kode' => $kodeJurnal,
                                    'branch_id' => Auth::user()->branch_id,
                                    'tanggal'   => dateStore($req->tanggal),
                                    'ref'   => $kode,
                                    'metode_pembayaran'   => $req->metode_pembayaran,
                                    'nama_bank' => $req->jenis_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                                    'nomor_kartu' => $req->jenis_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                                    'jenis'   => 'KASIR',
                                    'dk'    => 'KREDIT',
                                    'description'    => 'PENGELUARAN Stok ' .  $produkObat->name,
                                    'nominal'   => $stock->getData()->total,
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                ]);
                        }
                        if ($req->rekam_medis_pasien_id[$i] != 'NON') {
                            if ($d == 'mo_produk_obat') {
                                $this->model->rekamMedisResep()
                                    ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id[$i])
                                    ->where('produk_obat_id', $req->ref[$i])
                                    ->update([
                                        'status_pembayaran_obat' => 'Done'
                                    ]);
                            }

                            if ($d == 'mo_kategori_obat') {
                                $this->model->rekamMedisResep()
                                    ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id[$i])
                                    ->where('kategori_obat_id', $req->ref[$i])
                                    ->update([
                                        'status_pembayaran_obat' => 'Done'
                                    ]);
                            }
                        }


                        if (isset($req->pasien_id)) {
                            $pasien_id = $req->pasien_id;
                        } else {
                            if ($req->pasiens_id[$i] == 'NON') {
                                $pasien_id = null;
                            } else {
                                $pasien_id = $req->pasiens_id[$i];
                            }
                        }

                        if ($req->jenis_stock[$i] == 'PICKUP') {
                            $this->model->pendaftaran()
                                ->where('id', $req->ref[$i])
                                ->update([
                                    'status_pembayaran_penjemputan' => true
                                ]);
                        }

                        $this->model->kasirDetail()
                            ->create([
                                'kasir_id'  => $id,
                                'id'    => $i + 1,
                                'table' => $d,
                                'ref'   => $req->ref[$i],
                                'stock' => $req->stock[$i],
                                'jenis_stock' => $req->jenis_stock[$i],
                                'harga' => convertNumber($req->harga[$i]),
                                'pasien_id' => $pasien_id,
                                'rekam_medis_pasien_id' => $req->rekam_medis_pasien_id[$i] != 'NON' ? $req->rekam_medis_pasien_id[$i] : null,
                                'qty'   => $req->qty[$i],
                                'bruto' =>  convertNumber($req->bruto[$i]),
                                'diskon_penyesuaian' =>  $req->diskon_penyesuaian[$i] ?? 0,
                                'nilai_diskon_penyesuaian' =>  convertNumber($req->nilai_diskon_penyesuaian[$i]),
                                'sub_total' =>  convertNumber($req->sub_total[$i]),
                                // 'description' =>  $req->rekam_medis_pasien_id[$i] != 'NON' ? null : 'ITEM DARI KASIR',
                            ]);
                    }
                }

                $sisaPembayaran = convertNumber($req->sisa_pembayaran) - convertNumber($req->diterima);
                if (convertNumber($req->diterima) > 0) {
                    // $idJurnal = $this->model->jurnal()->max('id') + 1;
                    // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);

                    $this->model->jurnal()
                        ->create([
                            // 'id'    => $idJurnal,
                            'kode'  => generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode,
                            // 'kode' => $kodeJurnal,
                            'branch_id' => Auth::user()->branch_id,
                            'tanggal'   => dateStore($req->tanggal),
                            'ref'   => $kode,
                            'jenis'   => 'KASIR',
                            'dk'    => 'DEBET',
                            'description'    => 'PEMBAYARAN ATAS NAMA ' . $namaMember,
                            'nominal'   => $sisaPembayaran <= 0 ? convertNumber($req->sisa_pembayaran) : convertNumber($req->diterima),
                            'metode_pembayaran' => $req->metode_pembayaran,
                            'nama_bank' => $req->metode_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                            'nomor_kartu' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    $this->model->kasirPembayaran()
                        ->create([
                            'kasir_id'   => $id,
                            'id'     => $this->model->kasirPembayaran()->where('kasir_id', $id)->max('id') + 1,
                            'ref'    => $kode,
                            'nilai_pembayaran'   => $sisaPembayaran <= 0 ? convertNumber($req->sisa_pembayaran) : convertNumber($req->diterima),
                            'keterangan'     => 'PEMBAYARAN ATAS NAMA ' . $req->nama_owner,
                            'jenis_pembayaran' => $req->metode_pembayaran,
                            'nama_bank' => $req->metode_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                            'nomor_kartu' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                            'nomor_transaksi' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_transaksi : null,
                            'created_by'     => me(),
                            'updated_by'     => me(),
                        ]);
                } else if ($req->type_kasir != 'Normal') {
                    $this->model->kasirPembayaran()
                        ->create([
                            'kasir_id'   => $id,
                            'id'     => $this->model->kasirPembayaran()->where('kasir_id', $id)->max('id') + 1,
                            'ref'    => $kode,
                            'nilai_pembayaran'   => convertNumber($req->total_bayar),
                            'keterangan'     =>  $req->type_kasir,
                            'jenis_pembayaran' => 'HIBAH',
                            'nama_bank' => $req->metode_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                            'nomor_kartu' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                            'nomor_transaksi' => $req->metode_pembayaran != 'TUNAI' ? $req->nomor_transaksi : null,
                            'created_by'     => me(),
                            'updated_by'     => me(),
                        ]);
                }


                if ($sisaPembayaran < 0) {
                    $sisaPembayaran = 0;
                }


                if ($req->jenis_tab != 'transaksi_langsung') {
                    if (isset($req->rekam_medis_pasien_id)) {
                        $rekamMedisPasien = str_replace(';NON', '', implode(';', $req->rekam_medis_pasien_id));
                        $rekamMedisPasien = explode(';', $rekamMedisPasien);
                        $rekamMedisPasien = array_values(array_unique($rekamMedisPasien));

                        foreach ($req->rekam_medis_pasien_id as $i => $d) {
                            if ($d != 'NON') {
                                $this->model->rekamMedisPasien()
                                    ->where('id', $d)
                                    ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa'])
                                    ->where('status_pengambilan_obat', true)
                                    ->update([
                                        'status_pembayaran' => true,
                                    ]);
                            }
                        }
                    } else {
                        $this->model->rekamMedisPasien()
                            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa'])
                            ->where('status_pengambilan_obat', true)
                            ->where(function ($q) use ($req) {
                                if ($req->jenis_tab == 'Rawat Jalan') {
                                    $q->where('rawat_inap', false);
                                } else {
                                    $q->where('rawat_inap', true);
                                }
                            })
                            ->whereHas('Pasien', function ($q) use ($req) {
                                $q->where('owner_id', $req->owner_id);
                            })
                            ->update([
                                'status_pembayaran' => true,
                            ]);
                    }
                }


                $this->model->kasir()
                    ->find($id)
                    ->update([
                        'sisa_pelunasan' => $req->type_kasir == 'Normal' ? $sisaPembayaran : 0,
                        'langsung_lunas' => $sisaPembayaran == 0 ? true : false,
                        'penarikan_deposit' => $penarikanDeposit,
                        'catatan_kasir' => $req->catatan_kasir,
                    ]);

                DB::commit();
                return Response()->json(['status' => 1, 'message' => 'Berhasil melakukan checkout', 'id' => $id]);

            } catch (\Throwable $th) {
                DB::rollBack();
                return queryStatus($th->getCode());
            }
        });
    }

    public function print(Request $req)
    {
        $data = $this->model->kasir()
            ->with([
                'kasirDetail' => function ($q) {
                    $q->select('kasir_id', 'ref', 'table', 'harga', DB::raw("sum(sub_total) as sub_total"), DB::raw("sum(bruto) as bruto"), 'diskon_penyesuaian', DB::raw("sum(qty) as qty"), 'pasien_id', 'rekam_medis_pasien_id');
                    $q->groupBy('kasir_id', 'ref', 'table', 'diskon_penyesuaian', 'pasien_id', 'harga', 'rekam_medis_pasien_id');
                },
                'owner' => function ($q) {
                    $q->select('id', 'kode', 'telpon', 'komunitas');
                    $q->groupBy('id', 'kode', 'telpon', 'komunitas');
                },
                'owner.deposit' => function ($q) {
                    $q->select('id', 'owner_id');
                    $q->groupBy('id', 'owner_id');
                },
                'owner.deposit.depositMutasi' => function ($q) {
                    $q->select('deposit_id', 'created_at', DB::raw("SUM(CASE WHEN jenis_deposit = 'DEBET' THEN nilai ELSE 0 END) AS total_debet"), DB::raw("SUM(CASE WHEN jenis_deposit = 'KREDIT' THEN nilai ELSE 0 END) AS total_kredit"));
                    $q->groupBy('deposit_id', 'created_at');
                },
                'kasirDetail.rekamMedisPasien' => function ($q) {
                    $q->select('id', 'created_by');
                    $q->groupBy('id', 'created_by');
                },
                'kasirDetail.rekamMedisPasien.createdBy'
            ])
            ->find($req->id);

        if ($data->owner && $data->owner->deposit) {
            $tanggal = $data->tanggal;

            $data->owner->deposit->depositMutasi = $data->owner->deposit->depositMutasi->filter(function ($item) use ($tanggal) {
                return $item->created_at->toDateString() <= $tanggal;
            });

            $total_debet = $data->owner->deposit->depositMutasi->pluck('total_debet')->sum();
            $total_kredit = $data->owner->deposit->depositMutasi->pluck('total_kredit')->sum();
        
            $sisa_deposit = $total_debet - $total_kredit;
            $data->owner->deposit->sisa_deposit = $sisa_deposit;
        }

        $nama = 'E-INVOICE ' . $data->kode . '-' . Carbon::parse($data->tanggal)->format('Y-m-d') . '.pdf';

        $pdf = PDF::loadView('transaksi/pembayaran/print', compact('data'))
            ->setPaper('a4', 'potrait');
        return $pdf->stream($nama);
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

    public function printKwitansi(Request $req)
    {
        $data = $this->model->kasir()->find($req->id);
        $nama = 'E-RECEIPT ' . $data->kode . '-' . Carbon::parse($data->tanggal)->format('Y-m-d') . '.pdf';
        $pdf = PDF::loadView('transaksi/pembayaran/kwitansi', compact('data'))
            ->setPaper('a4', 'potrait');
        return $pdf->stream($nama);
    }

    public function sendInvoice(Request $req)
    {


        $validData = [
            'email' => 'required|email',
        ];

        $validRule = [
            'email.email' => 'Format email salah',
            'email.required' => 'Email harus diisi',
        ];

        $validator = Validator::make(
            $req->all(),
            $validData,
            $validRule,
        );

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
        }

        $data = $this->model->kasir()->findOrFail($req->id);
        $path = Storage::path('pdf');
        $nama = 'E-INVOICE ' . $data->kode . '-' . Carbon::parse($data->created_at)->format('Y-m-d') . '.pdf';
        $pdf = PDF::loadView('transaksi/pembayaran/print', compact('data'))
            ->setPaper('a4', 'potrait');

        if (!file_exists($path)) {
            $oldmask = umask(0);
            mkdir($path, 0777, true);
            umask($oldmask);
        }

        $pdf->save($path . '/' . $nama);
        $emailData = new SendInvoice($req->email, $data, $path . '/' . $nama);
        Mail::to($req->email)
            ->send($emailData);
        return Response()->json(['status' => 1, 'message' => 'Berhasil Mengirim Email ke ' . $req->email]);
    }

    // public function backToApotek(Request $req)
    // {

    //     // dd($req->all());
    //     $this->model->rekamMedisPasien()
    //         ->whereIn('id', $req->rekam_medis_pasien_id)
    //         ->update([
    //             'status_pengambilan_obat' => false,
    //             'kembali_ke_apotek' => 'Ya',
    //         ]);

    //     $this->model->rekamMedisResep()
    //         ->whereIn('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
    //         ->where('status_resep', 'Antrian')
    //         ->where('jenis_obat', 'non-racikan')
    //         ->update([
    //             'status_pembuatan_obat' => 'Undone',
    //         ]);

    //     foreach ($req->rekam_medis_pasien_id as $key => $value) {
    //         $this->notify->broadcastingAntrianApotekDariPembayaran($value);
    //     }

    //     return Response()->json(['status' => 1, 'message' => 'Berhasil Kembalikan Ke Apotek']);
    // }

    public function backToApotek(Request $req)
    {
        // dd($req->all());
       $this->model->rekamMedisPasien()
            ->whereIn('id', $req->rekam_medis_pasien_id)
            ->update([
                'status_pengambilan_obat' => false,
                'kembali_ke_apotek' => 'Ya',

                // add 16-Jan-2023
                'status_apoteker'   => 'revisi',
                'progress_by'       => null,
                //end

                'desc_kasir'        => $req->desc_kasir
            ]);

        $this->model->rekamMedisResep()
            ->whereIn('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
            ->where('status_resep', 'Antrian')
            ->where('jenis_obat', 'non-racikan')
            ->update([
                'status_pembuatan_obat' => 'Undone',
            ]);

        foreach ($req->rekam_medis_pasien_id as $key => $value) {
            $this->notify->broadcastingAntrianApotekDariPembayaran($value);
        }

        return Response()->json(['status' => 1, 'message' => 'Berhasil Kembalikan Ke Apotek']);
    }

    public function backToRanap(Request $req)
    {
        // dd($req->all());
        $this->model->rekamMedisPasien()
            ->whereIn('id', $req->rekam_medis_pasien_id)
            ->update([
                'status_pengambilan_obat' => false,
                'status_pemeriksaan' => 'Rawat Inap',
                'tanggal_keluar' => null,
                'status_kepulangan' => null,
            ]);

        $this->model->kamarRawatInapDanBedahDetail()
            ->whereIn('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
            ->where('status_pindah', false)
            ->where('status', 'Done')
            ->update([
                'status' => 'In Use',
                'tanggal_keluar' => null,
            ]);

        $this->model->pasien_meninggal()
            ->whereIn('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
            ->delete();

        foreach ($req->rekam_medis_pasien_id as $key => $value) {
            $text = '<b>' . Auth::user()->name . '</b> mengambalikan ke ranap';
            $this->addRekamMedisLogHistory($value, $text, 'mp_rekam_medis_pasien', $value);
        }

        return Response()->json(['status' => 1, 'message' => 'Berhasil Kembalikan Ke Ranap']);
    }

    public function refreshDataStock(Request $req)
    {
        $item = [];
        $itemNonObat = [];

        foreach ($req->ref as $i => $d) {
            if ($req->jenis_stock[$i] == 'OBAT') {
                $tempItem = $this->model->produkObat()
                    ->select('id', 'name', DB::raw("cast('OBAT' as varchar(20))  AS type"), 'harga')
                    ->where('id', $d)
                    ->with([
                        'StockFirst' => function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        }
                    ])
                    ->where('status', true)
                    ->first();
                array_push($item, $tempItem);
            } else {
                $tempItemNonObat = $this->model->itemNonObat()
                    ->select('id', 'name', DB::raw("cast('NON OBAT' as varchar(20))  AS type"), 'harga')
                    ->where('id', $d)
                    ->with([
                        'StockFirst' => function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        }
                    ])
                    ->where('status', true)
                    ->where('kategori', '=', 'DIJUAL BEBAS')
                    ->first();
                array_push($itemNonObat, $tempItemNonObat);
            }
        }

        return Response()->json([
            'status' => 1,
            'item_non_obat' => $itemNonObat,
            'item' => $item
        ]);
    }

    public function getPasienPembayaran(Request $req)
    {
        $pasien = $this->model->owner()
            ->whereHas('singleRekamMedisPasien', function ($q) {
                $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                $q->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    } else {
                        $q->where('branch_id', request()->input('branch_id'));
                    }
                });
                $q->where('status_pengambilan_obat', true);
                $q->where('status_pembayaran', false);
                $q->where('rawat_inap', false);
                // $q->whereHas('pendaftaran', function ($q) {
                //     $q->where('status', 'Completed');
                // });
                $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
            })
            ->with(['singleRekamMedisPasien' => function ($q) {
                $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                $q->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    } else {
                        $q->where('branch_id', request()->input('branch_id'));
                    }
                });
                $q->where('status_pengambilan_obat', true);
                $q->where('status_pembayaran', false);
                $q->where('rawat_inap', false);
                // $q->whereHas('pendaftaran', function ($q) {
                //     $q->where('status', 'Completed');
                // });
                $q->orderBy('updated_at', 'DESC');
                $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
            }])
            ->get();
        $pasien = $pasien->sortBy(function ($data, $key) {
            return $data->singleRekamMedisPasien->updated_at;
        });

        $pasienRawatInap = $this->model->owner()
            ->whereHas('singleRekamMedisPasien', function ($q) {
                $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                $q->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    } else {
                        $q->where('branch_id', request()->input('branch_id'));
                    }
                });
                $q->where('status_pengambilan_obat', true);
                $q->where('status_pembayaran', false);
                $q->where('rawat_inap', true);
                // $q->whereHas('pendaftaran', function ($q) {
                //     $q->where('status', 'Completed');
                // });
                $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
            })
            ->with(['singleRekamMedisPasien' => function ($q) {
                $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                $q->where(function ($q) {
                    if (!Auth::user()->akses('global', null, false)) {
                        $q->whereHas('Pendaftaran', function ($q) {
                            $q->where('branch_id', Auth::user()->branch_id);
                        });
                    } else {
                        $q->where('branch_id', request()->input('branch_id'));
                    }
                });
                $q->where('status_pengambilan_obat', true);
                $q->where('status_pembayaran', false);
                $q->where('rawat_inap', true);
                // $q->whereHas('pendaftaran', function ($q) {
                //     $q->where('status', 'Completed');
                // });
                $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
            }])
            ->get();

        $pasienRawatInap = $pasienRawatInap->sortBy(function ($data, $key) {
            return $data->singleRekamMedisPasien->updated_at;
        });

        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('id', 1)
            // ->where('dokter', me())
            ->first();

        // generateStock();
        return view('transaksi/pembayaran/list_pasien_pembayaran', compact('pasien', 'pasienRawatInap'));
    }
}

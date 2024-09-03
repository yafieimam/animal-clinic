<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Mail\SendInvoice;
use App\Models\Modeler;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Image;
use Yajra\DataTables\Facades\DataTables;

class TagihanSementaraController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);

        $pasien = $this->model->owner()
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
                    $q->where('status_pembayaran', false);
                    $q->where('rawat_inap', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
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
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
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
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
                    $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                }]);
            }])
            ->get();

        $pasienRawatInap = $this->model->owner()
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
                    $q->where('status_pembayaran', false);
                    $q->where('rawat_inap', true);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
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
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
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
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
                    $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                }]);
            }])
            ->get();

        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('id', 1)
            // ->where('dokter', me())
            ->first();


        return view('transaksi/tagihan_sementara/tagihan_sementara', compact('pasien', 'data', 'pasienRawatInap'));
    }

    public function datatable(Request $req)
    {
        if ($req->jenis_data == 'Owner') {
            $data = $this->model->owner()
                ->whereHas('pasien', function ($q) {
                    $q->whereHas('rekamMedisPasien', function ($q) {
                        $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                        $q->where(function ($q) {
                            if (!Auth::user()->akses('global', null, false)) {
                                $q->whereHas('Pendaftaran', function ($q) {
                                    $q->where('branch_id', Auth::user()->branch_id);
                                });
                            }
                        });
                        $q->where('status_pembayaran', false);
                        $q->where('rawat_inap', true);
                        $q->whereHas('pendaftaran', function ($q) {
                            $q->where('status', 'Completed');
                        });
                        $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                    });
                })
                ->with(['pasien' => function ($q) {
                    $q->whereHas('rekamMedisPasien', function ($q) {
                        $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                        $q->where(function ($q) {
                            if (!Auth::user()->akses('global', null, false)) {
                                $q->whereHas('Pendaftaran', function ($q) {
                                    $q->where('branch_id', Auth::user()->branch_id);
                                });
                            }
                        });
                        $q->where('status_pembayaran', false);
                        $q->whereHas('pendaftaran', function ($q) {
                            $q->where('status', 'Completed');
                        });
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
                        $q->where('status_pembayaran', false);
                        $q->whereHas('pendaftaran', function ($q) {
                            $q->where('status', 'Completed');
                        });
                        $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                    }]);
                }])
                ->get();


            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<button class="btn btn-primary" onclick="getPembayaran(\'' . $data->id . '\')">Pilih</button>';
                })
                ->rawColumns(['aksi', 'status', 'icon', 'sequence'])
                ->addIndexColumn()
                ->make(true);
        } else {
            $data = $this->model->pasien()
                ->where(function ($q) {
                    $q->whereHas('rekamMedisPasien', function ($q) {
                        $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                        $q->where(function ($q) {
                            if (!Auth::user()->akses('global', null, false)) {
                                $q->whereHas('Pendaftaran', function ($q) {
                                    $q->where('branch_id', Auth::user()->branch_id);
                                });
                            }
                        });
                        $q->where('status_pembayaran', false);
                        $q->where('rawat_inap', true);
                        $q->whereHas('pendaftaran', function ($q) {
                            $q->where('status', 'Completed');
                        });
                        $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                    });
                })
                ->with(['rekamMedisPasien' => function ($q) {
                    $q->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa']);
                    $q->where(function ($q) {
                        if (!Auth::user()->akses('global', null, false)) {
                            $q->whereHas('Pendaftaran', function ($q) {
                                $q->where('branch_id', Auth::user()->branch_id);
                            });
                        }
                    });
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
                    $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                }])
                ->get();


            return DataTables::of($data)
                ->addColumn('aksi', function ($data) {
                    return '<button class="btn btn-primary" onclick="getPembayaran(\'' . $data->id . '\')">Pilih</button>';
                })
                ->rawColumns(['aksi', 'status', 'icon', 'sequence'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function history()
    {
        return view('transaksi/tagihan_sementara/history_kasir');
    }

    public function datatableDeposit(Request $req)
    {
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

    public function getTagihanSementara(Request $req)
    {
        $ownerId = $req->id;
        if ($req->jenis_data == 'Pasien') {
            $ownerId = $this->model->pasien()->find($req->id)->owner_id;
        }

        $data = $this->model->owner()
            ->where('id', $ownerId)
            ->whereHas('pasien', function ($q) use ($req) {
                if ($req->jenis_data == 'Pasien') {
                    $q->where('id', $req->id);
                }
                $q->whereHas('rekamMedisPasien', function ($q) {
                    $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                    $q->where(function ($q) {
                        if (!Auth::user()->akses('global', null, false)) {
                            $q->whereHas('Pendaftaran', function ($q) {
                                $q->where('branch_id', Auth::user()->branch_id);
                            });
                        }
                    });
                    $q->where('status_pembayaran', false);
                    $q->where('rawat_inap', true);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
                    $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                });
            })
            ->with(['pasien' => function ($q) use ($req) {
                if ($req->jenis_data == 'Pasien') {
                    $q->where('id', $req->id);
                }
                $q->whereHas('rekamMedisPasien', function ($q) {
                    $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                    $q->where(function ($q) {
                        if (!Auth::user()->akses('global', null, false)) {
                            $q->whereHas('Pendaftaran', function ($q) {
                                $q->where('branch_id', Auth::user()->branch_id);
                            });
                        }
                    });
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
                    $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                });
                $q->with(['rekamMedisPasien' => function ($q) {
                    $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                    $q->where(function ($q) {
                        if (!Auth::user()->akses('global', null, false)) {
                            $q->whereHas('Pendaftaran', function ($q) {
                                $q->where('branch_id', Auth::user()->branch_id);
                            });
                        }
                    });
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
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
                        'rekamMedisRekomendasiTindakanBedah' => function ($q) {
                            $q->select('rekam_medis_pasien_id', 'tindakan_id', DB::raw("count(rekam_medis_pasien_id) as qty"), 'status');
                            $q->groupBy('rekam_medis_pasien_id', 'tindakan_id', 'status');
                        },
                        'kamarRawatInapDanBedahDetail' => function ($q) {
                            $q->orderBy('created_at', 'DESC');
                        }
                    ]);
                    $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                }]);
            }])
            ->first();
        $kode = $this->generateKode($req)->getData()->kode;

        $pendaftaranId = [];

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

        return view('transaksi/tagihan_sementara/template_data', compact('data', 'kode', 'penjemputan', 'req'));
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

    public function generateItem(Request $req)
    {
        $data = $this->model->rekamMedisPasien()->find($req->id);
        $item =    $this->model->itemNonObat()
            ->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->param) . '%')
            ->with([
                'StockFirst' => function ($q) use ($data) {
                    $q->where('branch_id', $data ? $data->Pendaftaran->branch_id : Auth::user()->branch_id);
                }
            ])
            ->where('status', true)
            ->where('kategori', '=', 'DIJUAL BEBAS')
            ->get();

        return view('transaksi/pembayaran/item_kasir', compact('item'));
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

    public function print(Request $req)
    {
        $ownerId = $req->id;
        if ($req->jenis_data == 'Pasien') {
            $ownerId = $this->model->pasien()->find($req->id)->owner_id;
        }
        $data = $this->model->owner()
            ->where('id', $ownerId)
            ->whereHas('pasien', function ($q) use ($req) {
                if ($req->jenis_data == 'Pasien') {
                    $q->whereId($req->id);
                }
                $q->whereHas('rekamMedisPasien', function ($q) {
                    $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                    $q->where(function ($q) {
                        if (!Auth::user()->akses('global', null, false)) {
                            $q->whereHas('Pendaftaran', function ($q) {
                                $q->where('branch_id', Auth::user()->branch_id);
                            });
                        }
                    });
                    $q->where('status_pembayaran', false);
                    $q->where('rawat_inap', true);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
                    $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                });
            })
            ->with(['pasien' => function ($q) use ($req) {
                if ($req->jenis_data == 'Pasien') {
                    $q->whereId($req->id);
                }
                $q->whereHas('rekamMedisPasien', function ($q) {
                    $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                    $q->where(function ($q) {
                        if (!Auth::user()->akses('global', null, false)) {
                            $q->whereHas('Pendaftaran', function ($q) {
                                $q->where('branch_id', Auth::user()->branch_id);
                            });
                        }
                    });
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
                    $q->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap']);
                });
                $q->with(['rekamMedisPasien' => function ($q) {
                    $q->whereIn('status_pemeriksaan', ['Rawat Inap']);
                    $q->where(function ($q) {
                        if (!Auth::user()->akses('global', null, false)) {
                            $q->whereHas('Pendaftaran', function ($q) {
                                $q->where('branch_id', Auth::user()->branch_id);
                            });
                        }
                    });
                    $q->where('status_pembayaran', false);
                    $q->whereHas('pendaftaran', function ($q) {
                        $q->where('status', 'Completed');
                    });
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
                        'rekamMedisRekomendasiTindakanBedah' => function ($q) {
                            $q->select('rekam_medis_pasien_id', 'tindakan_id', DB::raw("count(rekam_medis_pasien_id) as qty"));
                            $q->where('status', 'Done');
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
        $kode = $this->generateKode($req)->getData()->kode;
        $nama = 'E-INVOICE ' . $data->kode . '-' . Carbon::parse($data->tanggal)->format('Y-m-d') . '.pdf';

        $pendaftaranId = [];

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

        $pdf = PDF::loadView('transaksi/tagihan_sementara/print', compact('data', 'kode', 'penjemputan'))
            ->setPaper('a4', 'potrait');
        return $pdf->stream($nama);
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
        $emailData = new SendInvoice($data->email, $data, $path . '/' . $nama);
        Mail::to($data->email)
            ->send($emailData);
        return Response()->json(['status' => 1, 'message' => 'Berhasil Mengirim Email ke ' . $data->email]);
    }
}

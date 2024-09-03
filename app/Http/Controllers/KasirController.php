<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Image;

class KasirController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);

        $pasien = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa'])
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
            ->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap'])
            ->get();

        $pasienRawatInap = $this->model->rekamMedisPasien()
            ->where('status_pemeriksaan', 'Rawat Inap')
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->whereHas('rekamMedisResep', function ($q) {
                $q->where('status_pembuatan_obat', 'Undone');
                $q->where('status_resep', 'Langsung');
            })
            ->where('status_pengambilan_obat', false)
            ->where('status_pembayaran', false)
            ->whereHas('pendaftaran', function ($q) {
                $q->where('status', 'Completed');
            })
            ->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap'])
            ->get();

        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('id', 1)
            // ->where('dokter', me())
            ->first();
        return view('transaksi/kasir/kasir', compact('pasien', 'data'));
    }

    public function history()
    {
        return view('transaksi/kasir/history_kasir');
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

    public function getKasir(Request $req)
    {
        $data = $this->model->rekamMedisPasien()->find($req->id);
        $produkObat = null;
        if ($data) {
            $data = $this->model->rekamMedisPasien()
                ->with([
                    'RekamMedisResep' => function ($q) use ($data) {
                        $q->with([
                            'ProdukObat' => function ($q) use ($data) {
                                $q->with(
                                    [
                                        'StockFirst' => function ($q) use ($data) {
                                            $q->where('branch_id', $data->Pendaftaran->branch_id);
                                        }
                                    ]
                                );
                            }
                        ]);
                    }
                ])
                ->find($req->id);

            $produkObat = $this->model->produkObat()
                ->with([
                    'StockFirst' => function ($q) use ($data) {
                        $q->where('branch_id', $data->Pendaftaran->branch_id);
                    }
                ])
                ->where('status', true)
                ->get();
        }


        $kode = $this->generateKode($req)->getData()->kode;

        if ($req->edit == 'true') {
            return view('transaksi/kasir/template_data', compact('data', 'produkObat', 'kode', 'req'));
        } else {
            return view('transaksi/kasir/history_template_data', compact('data', 'produkObat', 'kode', 'req'));
        }
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

        return view('transaksi/kasir/item_kasir', compact('item'));
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

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            try {
                DB::beginTransaction();
                // DB::statement('LOCK TABLE t_jurnal, t_kasir, t_kasir_detail IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                $idRekamMedisPasien = $req->id;
                $data = $this->model->rekamMedisPasien()->find($idRekamMedisPasien);
                // Simpan Resep
                $id = $this->model->kasir()->max('id') + 1;
                $kode = $this->generateKode($req)->getData()->kode;

                if ($req->metode_pembayaran != 'TUNAI') {
                    if (convertNumber($req->pembayaran) != convertNumber($req->diterima)) {
                        DB::rollBack();
                        return Response()->json(['status' => 2, 'message' => 'Uang yang diterima tidak sama dengan pembayaran.']);
                    }
                }

                if (convertNumber($req->pembayaran) > convertNumber($req->diterima)) {
                    DB::rollBack();
                    return Response()->json(['status' => 2, 'message' => 'Uang kembali tidak boleh minus.']);
                }

                $this->model->kasir()
                    ->create([
                        'id'    => $id,
                        'kode'  => $kode,
                        'branch_id' => $data ? $data->Pendaftaran->branch_id : Auth::user()->branch_id,
                        'tanggal'   => dateStore($req->tanggal),
                        'rekam_medis_pasien_id' => $data ? $idRekamMedisPasien : null,
                        'nama_owner'    => $req->nama_owner,
                        'total_obat'    => convertNumber($req->total_obat),
                        'total_lain'    => convertNumber($req->total_lain),
                        'diskon_penyesuaian'    => convertNumber($req->diskon_penyesuaian),
                        'total_bayar'   => convertNumber($req->total_bayar),
                        'diskon'    => convertNumber($req->diskon),
                        'deposit'    => convertNumber($req->deposit),
                        'pembayaran'    => convertNumber($req->pembayaran),
                        'diterima'  => convertNumber($req->diterima),
                        'uang_kembali'  => convertNumber($req->uang_kembali),
                        'metode_pembayaran' => $req->metode_pembayaran,
                        'status'    => true,
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);


                if ($data) {
                    if (convertNumber($req->deposit) != 0) {
                        // $idJurnal = $this->model->jurnal()->max('id') + 1;
                        $kodeJurnal = generateKodeJurnal($data->Pendaftaran->Branch->kode)->getData()->kode;

                        $this->model->jurnal()
                            ->create([
                                // 'id'    => $idJurnal,
                                'kode'  => $kodeJurnal,
                                'branch_id' =>  $data->pendaftaran->branch_id,
                                'tanggal'   => dateStore($req->tanggal),
                                'ref'   => $kode,
                                'jenis'   => 'KASIR',
                                'dk'    => 'KREDIT',
                                'description'    => 'PENGURANGAN DEPOSIT ATAS NAMA ' . $data->Pasien->Owner->name,
                                'nominal'   => convertNumber($req->deposit),
                                'created_by'    => me(),
                                'updated_by'    => me(),
                            ]);

                        $this->model->jurnalDetail()
                            ->where('pasien_id', $data->Pasien->id)
                            ->where('status_deposit', 'Released')
                            ->update([
                                'status_deposit' => 'Done'
                            ]);
                    }
                }

                if (convertNumber($req->pembayaran) != 0) {
                    // $idJurnal = $this->model->jurnal()->max('id') + 1;
                    $kodeJurnal = generateKodeJurnal(($data ? $data->Pendaftaran->Branch->kode : Auth::user()->Branch->kode))->getData()->kode;
                    $this->model->jurnal()
                        ->create([
                            // 'id'    => $idJurnal,
                            'kode'  => $kodeJurnal,
                            'branch_id' => $data ? $data->pendaftaran->branch_id : Auth::user()->branch_id,
                            'tanggal'   => dateStore($req->tanggal),
                            'ref'   => $kode,
                            'jenis'   => 'KASIR',
                            'dk'    => 'DEBET',
                            'description'    => 'PEMBAYARAN PENGOBATAN ATAS NAMA ' . ($data ? $data->Pasien->Owner->name : $req->nama_owner),
                            'nominal'   => convertNumber($req->pembayaran),
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                }

                foreach ($req->table as $i => $d) {
                    if ($req->stock[$i] == 'YA') {
                        $stock = decreasingStock('NON OBAT', $req->ref[$i], $data ? $data->Pendaftaran->Branch->id : Auth::user()->Branch->id, $req->qty[$i], $kode);
                        $produkObat = $this->model->itemNonObat()->find($req->ref[$i]);
                        if (count($stock->getData()->mutasi) == 0) {
                            DB::rollBack();
                            return Response()->json(['status' => 2, 'message' => 'Stock untuk obat ' . $produkObat->name . ' sudah habis.']);
                        }

                        // $idJurnal = $this->model->jurnal()->max('id') + 1;
                        $kodeJurnal = generateKodeJurnal($data ? $data->Pendaftaran->Branch->kode : Auth::user()->Branch->kode)->getData()->kode;
                        $this->model->jurnal()
                            ->create([
                                // 'id'    => $idJurnal,
                                'kode'  => $kodeJurnal,
                                'branch_id' => ($data ? $data->pendaftaran->branch_id : Auth::user()->branch_id),
                                'tanggal'   => dateStore($req->tanggal),
                                'ref'   => $kode,
                                'jenis'   => 'KASIR',
                                'dk'    => 'KREDIT',
                                'description'    => 'PENGELUARAN STOCK ' .  $produkObat->name,
                                'nominal'   => $stock->getData()->total,
                                'created_by'    => me(),
                                'updated_by'    => me(),
                            ]);
                    }



                    $this->model->kasirDetail()
                        ->create([
                            'kasir_id'  => $id,
                            'id'    => $i + 1,
                            'table' => $d,
                            'ref'   => $req->ref[$i],
                            'stock' => $req->stock[$i],
                            'harga' => convertNumber($req->harga[$i]),
                            'qty'   => $req->qty[$i],
                            'sub_total' =>  convertNumber($req->sub_total[$i]),
                        ]);
                }
                if ($data) {
                    $this->model->rekamMedisPasien()->find($idRekamMedisPasien)
                        ->update([
                            'status_pembayaran' => true,
                        ]);
                }
                
                DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Berhasil melakukan checkout', 'id' => $id]);
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }
        });
    }

    public function print(Request $req)
    {
        $data = $this->model->kasir()->find($req->id);
        $nama = 'E-RECEIPT ' . $data->kode . '-' . Carbon::parse($data->tanggal)->format('Y-m-d') . '.pdf';
        $pdf = PDF::loadview('transaksi/kasir/print', compact('data'))
            ->setPaper('a4', 'potrait');
        return $pdf->stream($nama);
    }
}

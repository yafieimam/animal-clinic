<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Database\QueryException;

class PengeluaranStockController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_stock/pengeluaran_stock/pengeluaran_stock');
    }

    public function create(Request $req)
    {
        Auth::user()->akses('create', null, true);
        return view('management_stock/pengeluaran_stock/create_pengeluaran_stock');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->pengeluaranStock()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }
                if ($req->branch_tujuan_id != '') {
                    $q->where('branch_tujuan_id', $req->branch_tujuan_id);
                }
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_stock/pengeluaran_stock/action_button_pengeluaran_stock', compact('data'));
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('supplier', function ($data) {
                return $data->Supplier != null ? $data->Supplier->name  : "-";
            })
            ->addColumn('jumlah_item', function ($data) {
                return $data->PengeluaranStockDetail->count();
            })
            ->addColumn('jumlah_qty', function ($data) {
                return $data->PengeluaranStockDetail->sum('qty');
            })
            ->addColumn('total', function ($data) {
                return number_format($data->PengeluaranStockDetail->sum('total_harga'));
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'OS-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->pengeluaranStock()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->pengeluaranStock()
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

    public function generateKodePenerimaan(Request $req)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'IS-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->penerimaanStock()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->penerimaanStock()
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


    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            try {
                DB::beginTransaction();
                // DB::statement('LOCK TABLE t_jurnal, ms_pengeluaran_stock, ms_penerimaan_stock, ms_pengeluaran_stock_detail, ms_penerimaan_stock_detail, ms_pengeluaran_stock_detail_mutasi IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');
                
                Auth::user()->akses('create', null, true);
                $id = $this->model->pengeluaranStock()->max('id') + 1;

                $kode = $this->generateKode($req)->getData()->kode;
                $file = $req->file('image');
                if ($file != null) {
                    $path = 'image/pengeluaran_stock';
                    $uuid =  Str::uuid($id)->toString();
                    $name = $uuid . '.' . $file->getClientOriginalExtension();
                    $foto = $path . '/' . $name;
                    if (is_file($foto)) {
                        unlink($foto);
                    }

                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }

                    Storage::disk('public_uploads')->put($foto, file_get_contents($file));
                } else {
                    $foto = null;
                }

                $this->model->pengeluaranStock()
                    ->create([
                        'id'    => $id,
                        'kode'  => $kode,
                        'jenis'  => $req->jenis,
                        'branch_id' => $req->branch_id,
                        'branch_tujuan_id'   => $req->branch_tujuan_id,
                        'tanggal_pengeluaran'    => $req->tanggal_pengeluaran,
                        'file_faktur'   => $foto,
                        'nomor_faktur'  => strtoupper($req->nomor_faktur),
                        'description'   => $req->keterangan,
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);

                if ($req->jenis == 'PINDAH CABANG') {
                    $idPenerimaanStock = $this->model->penerimaanStock()->max('id') + 1;
                    $this->model->penerimaanStock()
                        ->create([
                            'id'    => $idPenerimaanStock,
                            'kode'  => $this->generateKodePenerimaan($req)->getData()->kode,
                            'branch_id' => $req->branch_tujuan_id,
                            'description'   => $req->keterangan,
                            'pengeluaran_stock_id'   => $id,
                            'status'   => 'Belum Diterima',
                            'file_faktur'   => $foto,
                            'nomor_faktur'  => strtoupper($req->nomor_faktur),
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                }

                foreach ($req->jenis_item as $key => $value) {
                    $column = $value == 'OBAT' ? 'produk_obat_id' : 'item_non_obat_id';
                    $checkStock = $this->model->stock()
                        ->where('jenis_stock', $value)
                        ->where($column, $req->item_id[$key])
                        ->where('branch_id', $req->branch_id)
                        ->first();

                    $result = decreasingStock($checkStock->jenis_stock, $req->item_id[$key], $checkStock->branch_id, $req->qty[$key], $kode);

                    $this->model->pengeluaranStockDetail()
                        ->create([
                            'pengeluaran_stock_id'  => $id,
                            'id'    => $key + 1,
                            'jenis_stock'   => $value,
                            'produk_obat_id'    => $value == 'OBAT' ? $req->item_id[$key] : null,
                            'item_non_obat_id'  => $value == 'NON OBAT' ? $req->item_id[$key] : null,
                            'qty'   => $req->qty[$key],
                            'total_harga'   => $result->getData()->total,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    foreach ($result->getData()->mutasi as $i => $d) {
                        $this->model->pengeluaranStockDetailMutasi()
                            ->create([
                                'pengeluaran_stock_id'  => $id,
                                'pengeluaran_stock_detail_id'   => $key + 1,
                                'id'    => $i + 1,
                                'mutasi_stock_id'   => $d->id,
                                'harga_satuan'  => $d->harga,
                                'qty'   => $d->qty,
                                'total_harga'   => $d->total,
                                'created_by'    => me(),
                                'updated_by'    => me(),
                            ]);

                        if ($req->jenis == 'PINDAH CABANG') {
                            $this->model->penerimaanStockDetail()
                                ->create([
                                    'penerimaan_stock_id'   => $idPenerimaanStock,
                                    'id'    => $i + 1,
                                    'jenis_stock'   => $value,
                                    'produk_obat_id'    => $value == 'OBAT' ? $req->item_id[$key] : null,
                                    'item_non_obat_id'  => $value == 'NON OBAT' ? $req->item_id[$key] : null,
                                    'harga_satuan'  => $d->harga,
                                    'qty'   => $d->qty,
                                    'total_harga'   => $d->total,
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                ]);
                            // $this->pindahStock($value, $column, $d->item_id, $req->branch_tujuan_id, $d->qty, $d->harga, $d->total, $kode);
                        }
                    }

                    if ($req->jenis != 'PINDAH CABANG') {
                        // $idJurnal = $this->model->jurnal()->max('id') + 1;
                        $branch = $this->model->branch()->find($req->branch_id);
                        $kodeJurnal = generateKodeJurnal($branch->kode)->getData()->kode;
                        $this->model->jurnal()
                            ->create([
                                // 'id'    => $idJurnal,
                                'kode'  => $kodeJurnal,
                                'branch_id' => $req->branch_id,
                                'tanggal'   => dateStore($req->tanggal),
                                'ref'   => $kode,
                                'jenis'   => 'PENGELUARAN BARANG',
                                'dk'    => 'KREDIT',
                                'description'    => 'PENGELUARAN STOK ' .  $checkStock->ProdukObat ? $checkStock->ProdukObat->name : $checkStock->ItemNonObat->name,
                                'nominal'   => $result->getData()->total,
                                'created_by'    => me(),
                                'updated_by'    => me(),
                            ]);
                    }
                }

                DB::commit();
                
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan', 'kode' => $kode]);
                
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }
        });
    }

    public function pindahStock($jenisStock, $column, $itemId, $branchId, $qty, $hargaSatuan, $totalHarga, $kode)
    {
        try {
            DB::beginTransaction();
            $checkStock = $this->model->stock()
                ->where('jenis_stock', $jenisStock)
                ->where($column, $itemId)
                ->where('branch_id', $branchId)
                ->first();

            if (is_null($checkStock)) {
                $idStock = $this->model->stock()
                    ->max('id') + 1;

                $this->model->stock()
                    ->create([
                        'id'    => $idStock,
                        'jenis_stock'   => $jenisStock,
                        'branch_id' => $branchId,
                        'produk_obat_id'    => $jenisStock == 'OBAT' ? $itemId : null,
                        'item_non_obat_id'  => $jenisStock == 'NON OBAT' ? $itemId : null,
                        'qty'   => convertNumber($qty),
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);
            } else {
                $idStock = $checkStock->id;
                $this->model->stock()
                    ->find($checkStock->id)
                    ->update([
                        'qty'   => $checkStock->qty + convertNumber($qty),
                        'updated_by'    => me(),
                    ]);
            }

            // $idMutasiStock = $this->model->mutasiStock()
            //     ->max('id') + 1;

            $this->model->mutasiStock()
                ->create([
                    'stock_id'  => $idStock,
                    // 'id'    => $idMutasiStock,
                    'harga_satuan'  => convertNumber($hargaSatuan),
                    'total_harga'   => convertNumber($totalHarga),
                    'qty'   => convertNumber($qty),
                    'qty_tersisa'   => convertNumber($qty),
                    'referensi' => $kode,
                    'jenis' => 'PENERIMAAN',
                    'created_by'    => me(),
                    'updated_by'    => me(),
                ]);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
        }
    }

    public function revertStock(Request $req)
    {
        $data = $this->model->pengeluaranStock()->find($req->id);

        $this->model->mutasiStock()
            ->where('referensi', $data->kode)
            ->delete();

        revertStock($req->id,  'ms_pengeluaran_stock_detail_mutasi');

        foreach ($data->PengeluaranStockDetail as $i => $d) {
            $itemId = $d->jenis_stock == 'OBAT' ? $d->produk_obat_id : $d->item_non_obat_id;
            rekonStock($d->jenis_stock, $itemId);
        }
    }

    public function update(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('edit', null, true);
            $this->revertStock($req);

            $data = $this->model->pengeluaranStock()->find($req->id);
            $id = $req->id;

            $file = $req->file('image');
            if ($file != null) {
                $path = 'image/pengeluaran_stock';
                $uuid =  Str::uuid($id)->toString();
                $name = $uuid . '.' . $file->getClientOriginalExtension();
                $foto = $path . '/' . $name;
                if (is_file($foto)) {
                    unlink($foto);
                }

                if (!file_exists($path)) {
                    $oldmask = umask(0);
                    mkdir($path, 0777, true);
                    umask($oldmask);
                }

                Storage::disk('public_uploads')->put($foto, file_get_contents($file));
            } else {
                $foto = $data->file_faktur;
            }

            $this->model->pengeluaranStock()
                ->find($req->id)
                ->update([
                    'jenis'  => $req->jenis,
                    'branch_id' => $req->branch_id,
                    'branch_tujuan_id'   => $req->branch_tujuan_id,
                    'tanggal_pengeluaran'    => $req->tanggal_pengeluaran,
                    'file_faktur'   => $foto,
                    'nomor_faktur'  => strtoupper($req->nomor_faktur),
                    'description'   => $req->keterangan,
                    'updated_by'    => me(),
                ]);


            $this->model->penerimaanStock()
                ->where('pengeluaran_stock_id', $req->id)
                ->delete();

            if ($req->jenis == 'PINDAH CABANG') {
                $idPenerimaanStock = $this->model->penerimaanStock()->max('id') + 1;
                $this->model->penerimaanStock()
                    ->create([
                        'id'    => $idPenerimaanStock,
                        'kode'  => $this->generateKodePenerimaan($req)->getData()->kode,
                        'branch_id' => $req->branch_tujuan_id,
                        'description'   => $req->keterangan,
                        'pengeluaran_stock_id'   => $id,
                        'status'   => 'Belum Diterima',
                        'file_faktur'   => $foto,
                        'nomor_faktur'  => strtoupper($req->nomor_faktur),
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);
            }

            $this->model->pengeluaranStockDetail()->where('pengeluaran_stock_id', $req->id)->delete();
            $this->model->pengeluaranStockDetailMutasi()->where('pengeluaran_stock_id', $req->id)->delete();


            foreach ($req->jenis_item as $key => $value) {
                $column = $value == 'OBAT' ? 'produk_obat_id' : 'item_non_obat_id';
                $checkStock = $this->model->stock()
                    ->where('jenis_stock', $value)
                    ->where($column, $req->item_id[$key])
                    ->where('branch_id', $req->branch_id)
                    ->first();

                $result = decreasingStock($checkStock->jenis_stock, $req->item_id[$key], $checkStock->branch_id, $req->qty[$key], $req->kode);

                $this->model->pengeluaranStockDetail()
                    ->create([
                        'pengeluaran_stock_id'  => $id,
                        'id'    => $key + 1,
                        'jenis_stock'   => $value,
                        'produk_obat_id'    => $value == 'OBAT' ? $req->item_id[$key] : null,
                        'item_non_obat_id'  => $value == 'NON OBAT' ? $req->item_id[$key] : null,
                        'qty'   => $req->qty[$key],
                        'total_harga'   => $result->getData()->total,
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);

                foreach ($result->getData()->mutasi as $i => $d) {
                    $this->model->pengeluaranStockDetailMutasi()
                        ->create([
                            'pengeluaran_stock_id'  => $id,
                            'pengeluaran_stock_detail_id'   => $key + 1,
                            'id'    => $i + 1,
                            'mutasi_stock_id'   => $d->id,
                            'harga_satuan'  => $d->harga,
                            'qty'   => $d->qty,
                            'total_harga'   => $d->total,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    if ($req->jenis == 'PINDAH CABANG') {
                        $this->model->penerimaanStockDetail()
                            ->create([
                                'penerimaan_stock_id'   => $idPenerimaanStock,
                                'id'    => $i + 1,
                                'jenis_stock'   => $value,
                                'produk_obat_id'    => $value == 'OBAT' ? $req->item_id[$key] : null,
                                'item_non_obat_id'  => $value == 'NON OBAT' ? $req->item_id[$key] : null,
                                'harga_satuan'  => $d->harga,
                                'qty'   => $d->qty,
                                'total_harga'   => $d->total,
                                'created_by'    => me(),
                                'updated_by'    => me(),
                            ]);
                        // $this->pindahStock($value, $column, $d->item_id, $req->branch_tujuan_id, $d->qty, $d->harga, $d->total, $kode);
                    }
                }
            }

            return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah', 'kode' => $req->kode]);
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->pengeluaranStock()->where('id', $req->id)
                ->update([
                    'status' => $req->param
                ]);
            return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }

    public function edit($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('edit', null, true);
        $ps = $this->model->pengeluaranStock()
            ->findOrFail($id);
        $data = $this->model->pengeluaranStock()
            ->with(
                [
                    'PengeluaranStockDetail' => function ($q) use ($ps) {
                        $q->with(
                            [
                                'ProdukObat' => function ($q) use ($ps) {
                                    $q->withCount(['MutasiStock as stock' => function ($q) use ($ps) {
                                        $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                                        $q->where('jenis', 'PENERIMAAN');
                                        $q->whereHas('stock', function ($q) use ($ps) {
                                            $q->where('branch_id', $ps->branch_id);
                                        });
                                    }]);
                                },
                                'ItemNonObat' => function ($q) use ($ps) {
                                    $q->withCount(['MutasiStock as stock' => function ($q) use ($ps) {
                                        $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                                        $q->where('jenis', 'PENERIMAAN');
                                        $q->whereHas('stock', function ($q) use ($ps) {
                                            $q->where('branch_id', $ps->branch_id);
                                        });
                                    }]);
                                }
                            ]
                        );
                    }
                ]
            )
            ->findOrFail($id);
        return view('management_stock/pengeluaran_stock/edit_pengeluaran_stock', compact('data'));
    }


    public function print($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('print', null, true);
        $ps = $this->model->pengeluaranStock()
            ->findOrFail($id);
        $data = $this->model->pengeluaranStock()
            ->with(
                [
                    'PengeluaranStockDetail' => function ($q) use ($ps) {
                        $q->with(
                            [
                                'ProdukObat' => function ($q) use ($ps) {
                                    $q->withCount(['MutasiStock as stock' => function ($q) use ($ps) {
                                        $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                                        $q->where('jenis', 'PENERIMAAN');
                                        $q->whereHas('stock', function ($q) use ($ps) {
                                            $q->where('branch_id', $ps->branch_id);
                                        });
                                    }]);
                                },
                                'ItemNonObat' => function ($q) use ($ps) {
                                    $q->withCount(['MutasiStock as stock' => function ($q) use ($ps) {
                                        $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                                        $q->where('jenis', 'PENERIMAAN');
                                        $q->whereHas('stock', function ($q) use ($ps) {
                                            $q->where('branch_id', $ps->branch_id);
                                        });
                                    }]);
                                }
                            ]
                        );
                    }
                ]
            )
            ->findOrFail($id);

        $pdf = PDF::loadview('management_stock/pengeluaran_stock/print_pengeluaran_stock', compact('data'))->setPaper('a4', 'potrait');
        return $pdf->stream('Nota Pengeluaran Stock-' . $data->kode . '-' . carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function lihat($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('edit', null, true);
        $ps = $this->model->pengeluaranStock()
            ->findOrFail($id);
        $data = $this->model->pengeluaranStock()
            ->with(
                [
                    'PengeluaranStockDetail' => function ($q) use ($ps) {
                        $q->with(
                            [
                                'ProdukObat' => function ($q) use ($ps) {
                                    $q->withCount(['MutasiStock as stock' => function ($q) use ($ps) {
                                        $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                                        $q->where('jenis', 'PENERIMAAN');
                                        $q->whereHas('stock', function ($q) use ($ps) {
                                            $q->where('branch_id', $ps->branch_id);
                                        });
                                    }]);
                                },
                                'ItemNonObat' => function ($q) use ($ps) {
                                    $q->withCount(['MutasiStock as stock' => function ($q) use ($ps) {
                                        $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                                        $q->where('jenis', 'PENERIMAAN');
                                        $q->whereHas('stock', function ($q) use ($ps) {
                                            $q->where('branch_id', $ps->branch_id);
                                        });
                                    }]);
                                }
                            ]
                        );
                    }
                ]
            )
            ->findOrFail($id);
        return view('management_stock/pengeluaran_stock/lihat_pengeluaran_stock', compact('data'));
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete');
            $data = $this->model->pengeluaranStock()->find($req->id);
            $allowDelete = true;
            if ($data->jenis == 'PINDAH CABANG') {
                if ($data->PenerimaanStock->status != 'Belum Diterima') {
                    $allowDelete = false;
                }
            }

            if ($allowDelete) {
                $this->revertStock($req);
                $this->model->pengeluaranStock()->find($req->id)->delete();

                $this->model->penerimaanStock()
                    ->where('pengeluaran_stock_id', $req->id)
                    ->delete();
                return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
            } else {
                return Response()->json(['status' => 2, 'message' => 'Branch tujuan telah menerima item ini.']);
            }
        });
    }

    public function select2(Request $req)
    {

        $id = isset($req->id) ? $req->id : 0;

        switch ($req->param) {
            case 'supplier_id':
                return $this->model->supplier()
                    ->select('id', DB::raw("name as text"), 'ms_supplier.*')
                    ->where('status', true)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            case 'item_id':
                if ($req->jenis_item == 'OBAT') {
                    return $this->model->produkObat()
                        ->select('id', DB::raw("name as text"), 'mo_produk_obat.*')
                        ->with(['Satuan'])
                        ->withCount(['MutasiStock as stock' => function ($q) use ($req) {
                            $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                            $q->where('jenis', 'PENERIMAAN');
                            $q->whereHas('stock', function ($q) use ($req) {
                                $q->where('branch_id', $req->branch_id);
                            });
                        }])
                        ->withCount(['PengeluaranStockDetail as reverse' => function ($q) use ($id) {
                            $q->select(DB::raw('coalesce(sum(qty),0)'));
                            $q->where('pengeluaran_stock_id', $id);
                        }])
                        ->where('status', true)
                        ->where(function ($q) use ($req) {
                            $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                        })
                        ->paginate(10);
                } elseif ($req->jenis_item == 'NON OBAT') {
                    return $this->model->itemNonObat()
                        ->select('id', DB::raw("name as text"), 'ms_item_non_obat.*')
                        ->with(['Satuan'])
                        ->withCount(['MutasiStock as stock' => function ($q) use ($req) {
                            $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                            $q->where('jenis', 'PENERIMAAN');
                            $q->where('branch_id', $req->branch_id);
                        }])
                        ->withCount(['PengeluaranStockDetail as reverse' => function ($q) use ($id) {
                            $q->select(DB::raw('coalesce(sum(qty),0)'));
                            $q->where('pengeluaran_stock_id', $id);
                        }])

                        ->where('status', true)
                        ->where(function ($q) use ($req) {
                            $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                        })
                        ->paginate(10);
                }

            default:
                # code...
                break;
        }
    }
}

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
use Illuminate\Database\QueryException;

class PenerimaanStockController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_stock/penerimaan_stock/penerimaan_stock');
    }

    public function create(Request $req)
    {
        Auth::user()->akses('create', null, true);
        return view('management_stock/penerimaan_stock/create_penerimaan_stock');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->penerimaanStock()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }
            })
            ->orderBy('tanggal_terima', 'DESC')
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                $qty = 0;
                $currentQty = 0;

                foreach ($data->PenerimaanStockDetail as $i => $d) {
                    $qty += $d->qty;
                    if ($d->MutasiStock) {
                        $currentQty += $d->MutasiStock->qty_tersisa;
                    }
                }

                if ($data->status == 'Belum Diterima') {
                    $status = 'Belum Diterima';
                }

                if ($data->PengeluaranStock) {
                    $status = 'Stok Belum Diterima';
                } else {
                    if ($qty != $currentQty) {
                        $status = 'Sudah Terpakai';
                    } else {
                        $status = 'Bisa Diedit';
                    }
                }
                return view('management_stock/penerimaan_stock/action_button_penerimaan_stock', compact('data', 'status'));
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('supplier', function ($data) {
                if ($data->PengeluaranStock) {
                    return $data->PengeluaranStock != null ? $data->PengeluaranStock->Branch->kode . ' ' . $data->PengeluaranStock->Branch->lokasi  : "-";
                } else {
                    return $data->Supplier != null ? $data->Supplier->name  : "-";
                }
            })
            ->addColumn('jumlah_item', function ($data) {
                return $data->PenerimaanStockDetail->count();
            })
            ->addColumn('jumlah_qty', function ($data) {
                return $data->PenerimaanStockDetail->sum('qty');
            })
            ->addColumn('total', function ($data) {
                return number_format($data->PenerimaanStockDetail->sum('total_harga'));
            })
            ->addColumn('tanggal_terima', function ($data) {
                if ($data->status == 'Belum Diterima') {
                    return '<span class="badge badge-danger">Belum Diterima</span>';
                }
                return $data->tanggal_terima != null ? $data->tanggal_terima : 'Belum Diterima';
            })
            ->addColumn('nomor_faktur', function ($data) {
                // if ($data->status == 'Belum Diterima') {
                //     return '<span class="badge badge-danger">Belum Diterima</span>';
                // }
                return $data->nomor_faktur;
            })
            ->addColumn('status', function ($data) {
                $qty = 0;
                $currentQty = 0;

                foreach ($data->PenerimaanStockDetail as $i => $d) {
                    $qty += $d->qty;
                    if ($d->MutasiStock) {
                        $currentQty += $d->MutasiStock->qty_tersisa;
                    }
                }

                if ($data->status == 'Belum Diterima') {
                    return '<span class="btn btn-danger">Belum Diterima</span>';
                }
                if ($data->PengeluaranStock) {
                    return '<span class="btn btn-primary">Stock Sudah Diterima</span>';
                } else {
                    if ($qty != $currentQty) {
                        return '<span class="btn btn-warning">Stock Sudah Terpakai</span>';
                    } else {
                        return '<span class="btn btn-info">Bisa Diedit</span>';
                    }
                }
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'tanggal_terima', 'nomor_faktur'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Request $req)
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

    public function store(Request $req)
    {
        // return DB::transaction(function () use ($req) {
        try {
            DB::beginTransaction();
            generateStock();
            Auth::user()->akses('create', null, true);
            $id = $this->model->penerimaanStock()->max('id') + 1;

            $kode = $this->generateKode($req)->getData()->kode;

            $file = $req->file('image');
            if ($file != null) {
                $path = 'image/penerimaan_stock';
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
            $this->model->penerimaanStock()
                ->create([
                    'id'    => $id,
                    'kode'  => $kode,
                    'branch_id' => $req->branch_id,
                    'supplier_id'   => $req->supplier_id,
                    'tanggal_terima'    => $req->tanggal_terima,
                    'file_faktur'   => $foto,
                    'nomor_faktur'  => strtoupper($req->nomor_faktur),
                    'description'   => $req->keterangan,
                    'status'   => 'Sudah Diterima',
                    'created_by'    => me(),
                    'updated_by'    => me(),
                ]);

            foreach ($req->jenis_item as $key => $value) {
                $column = $value == 'OBAT' ? 'produk_obat_id' : 'item_non_obat_id';
                $checkStock = $this->model->stock()
                    ->where('jenis_stock', $value)
                    ->where($column, $req->item_id[$key])
                    ->where('branch_id', $req->branch_id)
                    ->first();

                if (is_null($checkStock)) {
                    $idStock = $this->model->stock()
                        ->max('id') + 1;

                    $this->model->stock()
                        ->create([
                            'id'    => $idStock,
                            'jenis_stock'   => $value,
                            'branch_id' => $req->branch_id,
                            'produk_obat_id'    => $value == 'OBAT' ? $req->item_id[$key] : null,
                            'item_non_obat_id'  => $value == 'NON OBAT' ? $req->item_id[$key] : null,
                            'qty'   => convertNumber($req->qty[$key]),
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                } else {
                    $idStock = $checkStock->id;
                    $this->model->stock()
                        ->find($checkStock->id)
                        ->update([
                            'qty'   => $checkStock->qty + convertNumber($req->qty[$key]),
                            'updated_by'    => me(),
                        ]);
                }

                // $idMutasiStock = $this->model->mutasiStock()
                //     ->max('id') + 1;

                $mutasiStock = $this->model->mutasiStock()
                    ->create([
                        'stock_id'  => $idStock,
                        // 'id'    => $idMutasiStock,
                        'harga_satuan'  => convertNumber($req->harga_satuan[$key]),
                        'total_harga'   => convertNumber($req->total_harga[$key]),
                        'qty'   => convertNumber($req->qty[$key]),
                        'qty_tersisa'   => convertNumber($req->qty[$key]),
                        'referensi' => $kode,
                        'jenis' => 'PENERIMAAN',
                        'expired_date'   => ($req->expired_date[$key]),
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);

                $this->model->penerimaanStockDetail()
                    ->create([
                        'penerimaan_stock_id'   => $id,
                        'id'    => $key + 1,
                        'jenis_stock'   => $value,
                        'produk_obat_id'    => $value == 'OBAT' ? $req->item_id[$key] : null,
                        'item_non_obat_id'  => $value == 'NON OBAT' ? $req->item_id[$key] : null,
                        'harga_satuan'  => convertNumber($req->harga_satuan[$key]),
                        'qty'   => convertNumber($req->qty[$key]),
                        'total_harga'   => convertNumber($req->total_harga[$key]),
                        // 'mutasi_stock_id'   => $idMutasiStock,
                        'mutasi_stock_id' => $mutasiStock->id,
                        'expired_date'   => ($req->expired_date[$key]),
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);
            }

            DB::commit();

            return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan', 'kode' => $kode]);
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
        }

        // });
    }

    public function revertStock(Request $req)
    {
        $data = $this->model->penerimaanStock()->find($req->id);

        foreach ($data->PenerimaanStockDetail as $i => $d) {
            $this->model->mutasiStock()
                ->where('id', $d->mutasi_stock_id)
                ->delete();

            $itemId = $d->jenis_stock == 'OBAT' ? $d->produk_obat_id : $d->item_non_obat_id;
            decreasingStock($d->jenis_stock, $itemId, $data->branch_id, 0);
        }
    }

    public function update(Request $req)
    {
        try {
            DB::beginTransaction();
        // return DB::transaction(function () use ($req) {
            Auth::user()->akses('create', null, true);
            generateStock();
            $this->revertStock($req);
            $data = $this->model->penerimaanStock()->find($req->id);

            $file = $req->file('image');
            if ($file != null) {
                $path = 'image/penerimaan_stock';
                $uuid =  Str::uuid($req->id)->toString();
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

            $this->model->penerimaanStock()
                ->find($req->id)
                ->update([
                    'branch_id' => $req->branch_id,
                    'supplier_id'   => $req->supplier_id,
                    'tanggal_terima'    => $req->tanggal_terima,
                    'file_faktur'   => $foto,
                    'nomor_faktur'  => strtoupper($req->nomor_faktur),
                    'description'   => $req->keterangan,
                    'status'   => 'Sudah Diterima',
                    'updated_by'    => me(),
                ]);

            $this->model->penerimaanStockDetail()->where('penerimaan_stock_id', $req->id)->delete();

            foreach ($req->jenis_item as $key => $value) {
                $column = $value == 'OBAT' ? 'produk_obat_id' : 'item_non_obat_id';

                $checkStock = $this->model->stock()
                    ->where('jenis_stock', $value)
                    ->where($column, $req->item_id[$key])
                    ->where('branch_id', $req->branch_id)
                    ->first();

                if (is_null($checkStock)) {
                    $idStock = $this->model->stock()
                        ->max('id') + 1;

                    $this->model->stock()
                        ->create([
                            'id'    => $idStock,
                            'jenis_stock'   => $value,
                            'branch_id' => $req->branch_id,
                            'produk_obat_id'    => $value == 'OBAT' ? $req->item_id[$key] : null,
                            'item_non_obat_id'  => $value == 'NON OBAT' ? $req->item_id[$key] : null,
                            'qty'   => convertNumber($req->qty[$key]),
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                } else {
                    $idStock = $checkStock->id;
                    $this->model->stock()
                        ->find($checkStock->id)
                        ->update([
                            'qty'   => $checkStock->qty + convertNumber($req->qty[$key]),
                            'updated_by'    => me(),
                        ]);
                }

                // $idMutasiStock = $this->model->mutasiStock()
                //     ->max('id') + 1;

                $mutasiStock = $this->model->mutasiStock()
                    ->create([
                        'stock_id'  => $idStock,
                        // 'id'    => $idMutasiStock,
                        'harga_satuan'  => convertNumber($req->harga_satuan[$key]),
                        'total_harga'   => convertNumber($req->total_harga[$key]),
                        'qty'   => convertNumber($req->qty[$key]),
                        'qty_tersisa'   => convertNumber($req->qty[$key]),
                        'referensi' => $req->kode,
                        'expired_date'   => ($req->expired_date[$key]),
                        'jenis' => 'PENERIMAAN',
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);

                $this->model->penerimaanStockDetail()
                    ->create([
                        'penerimaan_stock_id'   => $req->id,
                        'id'    => $key + 1,
                        'jenis_stock'   => $value,
                        'produk_obat_id'    => $value == 'OBAT' ? $req->item_id[$key] : null,
                        'item_non_obat_id'  => $value == 'NON OBAT' ? $req->item_id[$key] : null,
                        'harga_satuan'  => convertNumber($req->harga_satuan[$key]),
                        'qty'   => convertNumber($req->qty[$key]),
                        'total_harga'   => convertNumber($req->total_harga[$key]),
                        // 'mutasi_stock_id'   => $idMutasiStock,
                        'mutasi_stock_id' => $mutasiStock->id,
                        'expired_date'   => ($req->expired_date[$key]),
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);
            }

            DB::commit();

            return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah', 'kode' => $req->kode]);
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
        }

        // });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->penerimaanStock()->where('id', $req->id)
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
        $data = $this->model->penerimaanStock()->findOrFail($id);
        return view('management_stock/penerimaan_stock/edit_penerimaan_stock', compact('data'));
    }

    public function lihat($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('view');
        $data = $this->model->penerimaanStock()->findOrFail($id);
        return view('management_stock/penerimaan_stock/lihat_penerimaan_stock', compact('data'));
    }

    public function terima($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('edit', null, true);
        $data = $this->model->penerimaanStock()->findOrFail($id);
        return view('management_stock/penerimaan_stock/terima_penerimaan_stock', compact('data'));
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete');
            $this->revertStock($req);
            $this->model->penerimaanStock()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function select2(Request $req)
    {
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
                        ->with(['satuan'])
                        ->where('status', true)
                        ->where(function ($q) use ($req) {
                            $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                        })
                        ->paginate(10);
                } elseif ($req->jenis_item == 'NON OBAT') {
                    return $this->model->itemNonObat()
                        ->select('id', DB::raw("name as text"), 'ms_item_non_obat.*')
                        ->with(['satuan'])
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

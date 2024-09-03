<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
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
        return view('management_stock/stock/stock');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->stock()
            ->where(function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    if ($req->branch_id != '') {
                        $q->where('branch_id', $req->branch_id);
                    }
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->jenis_item != '') {
                    if ($req->jenis_item == 'NON OBAT') {
                        if ($req->item_id != '') {
                            $q->where('item_non_obat_id', $req->item_id);
                        }
                        $q->has('ItemNonObat');
                    } else {
                        if ($req->item_id != '') {
                            $q->where('produk_obat_id', $req->item_id);
                        }
                        $q->has('ProdukObat');
                    }
                }
            })
            ->orderBy('created_at', 'ASC');

        return Datatables::eloquent($data)
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->filterColumn('branch', function ($q, $kw) {
                $q->whereHas('Branch', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(kode)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->filterColumn('item', function ($q, $kw) {
                $q->whereHas('ProdukObat', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
                $q->orWhereHas('ItemNonObat', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('item', function ($data) {
                if ($data->ProdukObat) {
                    return $data->ProdukObat->name . ' ' . $data->ProdukObat->dosis;
                }

                if ($data->ItemNonObat) {
                    return $data->ItemNonObat->name;
                }
            })
            ->addColumn('qty', function ($data) {
                return \App\Models\MutasiStock::where('stock_id', $data->id)->where('jenis', 'PENERIMAAN')->sum('qty');
            })
            ->addColumn('sisa_qty', function ($data) {
                $qty =  \App\Models\MutasiStock::where('stock_id', $data->id)->where('jenis', 'PENERIMAAN')->sum('qty_tersisa');
                if ($qty < 10) {
                    return '<input type="hidden" value="' . $qty . '" ><label  style="color:red">' . $qty * 1 . '</label>';
                } else {
                    return '<input type="hidden" value="' . $qty . '" ><label  style="color:green">' . $qty * 1 . '</label>';
                }
            })
            ->addColumn('request', function ($data) {
                $nama =  $data->jenis_stock == 'OBAT' ? $data->ProdukObat->name : $data->ItemNonObat->name;
                return '<button class="btn btn-primary btn-round btn-xs" onclick="requestStock(\'' . $data->id . '\',\'' . $data->branch_id . '\',\'' . $nama . '\')">Request Stok</button>';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'lokasi_cabang', 'jam_buka', 'request', 'sisa_qty'])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $stock = $this->model->stock()->find($req->id);
            $id = $this->model->permintaanStock()->max('id') + 1;
            $this->model->permintaanStock()
                ->create([
                    'id'    => $id,
                    'jenis_stock' => $stock->jenis_stock,
                    'branch_id' => $req->branch_id,
                    'produk_obat_id' => $stock->produk_obat_id,
                    'item_non_obat_id' => $stock->item_non_obat_id,
                    'qty' => $req->qty,
                    'status' => 'Released',
                    'created_by' => me(),
                    'updated_by' => me(),
                ]);

            $this->notify->broadcastingRequestStock($req);
            return Response()->json(['status' => 1, 'message' => 'Berhasil request stok']);
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->stock()->where('id', $req->id)
                ->update([
                    'status' => $req->param
                ]);
            return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }

    public function edit(Request $req)
    {
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model->stock()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            try {
                $this->model->stock()->find($req->id)->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }


    public function rekonStock()
    {
        rekonStockCabang();
    }
}

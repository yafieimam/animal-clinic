<?php

namespace App\Http\Controllers;

use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PermintaanStockController extends Controller
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
        return view('management_stock/permintaan_stock/permintaan_stock');
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        if ($data->status == 'Released') {
            $approve = '<li>' .
                '<a href="javascript:;" onclick="store(\'' . $data->id . '\',\'' . 'Approved' . '\')" class="dropdown-item text-info">' .
                '<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;Approve' .
                '</a>' .
                '</li>';

            $reject =  '<li>' .
                '<a href="javascript:;" onclick="store(\'' . $data->id . '\',\'' . 'Rejected' . '\')" class="dropdown-item text-danger">' .
                '<i class="fa fa-cancel"></i>&nbsp;&nbsp;&nbsp;Reject' .
                '</a>' .
                '</li>';


            return '<div class="dropdown">' .
                '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
                '<span class="w-5 h-5 flex items-center justify-center">' .
                '<i class="fa fa-bars"></i>' .
                '</span>' .
                '</button>' .
                '<div class="dropdown-menu w-40 ">' .
                '<ul class="dropdown-content">' .
                $approve .
                $reject .
                '</ul>' .
                '</div>' .
                '</div>';
        } else {
            return '-';
        }
    }

    public function datatable(Request $req)
    {
        $data = $this->model->permintaanStock()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if ($req->jenis_item != '') {
                    if ($req->jenis_item == 'NON OBAT') {
                        $q->where('item_non_obat_id', $req->item_id);
                    } else {
                        $q->where('produk_obat_id', $req->item_id);
                    }
                }
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            ->addColumn('status', function ($data) {
                if ($data->status == 'Released') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Belum Diapprove</div>';
                } elseif ($data->status == 'Approved') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Approved</div>';
                } elseif ($data->status == 'Rejected') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Rejected</div>';
                }
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->lokasi  : "-";
            })
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy != null ? $data->CreatedBy->name  : "-";
            })
            ->addColumn('updated_by', function ($data) {
                return $data->UpdatedBy != null ? $data->UpdatedBy->name  : "-";
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->addColumn('updated_at', function ($data) {
                if ($data->status == 'Released') {
                    return "-";
                }

                if ($data->status == 'Approved'){
                    return $data->updated_at;
                }

                if ($data->status == 'Rejected'){
                    return $data->updated_at;
                }

            })
            ->addColumn('item', function ($data) {
                if ($data->ProdukObat) {
                    return $data->ProdukObat->name . ' ' . $data->ProdukObat->dosis;
                }

                if ($data->ItemNonObat) {
                    return $data->ItemNonObat->name;
                }
            })
            ->addColumn('request', function ($data) {
                $nama =  $data->jenis_stock == 'OBAT' ? $data->ProdukObat->name : $data->ItemNonObat->name;
                return '<button class="btn btn-primary btn-round btn-xs" onclick="requestStock(\'' . $data->id . '\',\'' . $data->branch_id . '\',\'' . $nama . '\')">Request Stock</button>';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'lokasi_cabang', 'jam_buka', 'request', 'sisa_qty', 'created_at', 'updated_at', 'created_by', 'updated_by'])
            ->addIndexColumn()
            ->make(true);
    }

    public function confirmation(Request $req)
    {
        return DB::transaction(function () use ($req) {

            $stock = $this->model->stock()->find($req->id);
            $id = $this->model->permintaanStock()->max('id') + 1;
            $this->model->permintaanStock()
                ->find($req->id)
                ->update([
                    'status' => $req->param,
                    'updated_by' => me(),
                ]);

            $this->notify->broadcastingRequestStock($req);
            if ($req->param == 'Approved') {
                return Response()->json(['status' => 1, 'message' => 'Permintaan Stok Disetujui']);
            } else {
                return Response()->json(['status' => 1, 'message' => 'Permintaan Stok Ditolak']);
            }
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
}

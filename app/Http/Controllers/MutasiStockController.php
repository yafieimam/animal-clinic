<?php

namespace App\Http\Controllers;

use App\Exports\MutasiStockExport;
use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MutasiStockController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_stock/mutasi_stock/mutasi_stock');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->mutasiStock()
            ->whereHas('stock', function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
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
            ->where(function ($q) use ($req) {
                $q->where('created_at', '>=', $req->tanggal_awal);
                $q->where('created_at', '<=', $req->tanggal_akhir);
            })
            ->orderBy('created_at', 'ASC');

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {

                $parameter = Crypt::encrypt($data->id);

                return '<a href="' . route('mutasi-stock') . '?id=' . $parameter . '" class="badge badge-primary" style="color: black;width: 100px;"><i
                    class="fa fa-bars"></i></a>  ';
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('branch', function ($data) {
                return $data->Stock->Branch != null ? $data->Stock->Branch->kode . ' ' . $data->Stock->Branch->lokasi  : "-";
            })
            ->addColumn('item', function ($data) {
                if ($data->Stock->ProdukObat) {
                    return $data->Stock->ProdukObat->name . ' ' . $data->Stock->ProdukObat->dosis . ' MG';
                }

                if ($data->Stock->ItemNonObat) {
                    return $data->Stock->ItemNonObat->name;
                }
            })
            ->filterColumn('branch', function ($q, $kw) {
                $q->whereHas('Stock', function ($query) use ($kw) {
                    $query->whereHas('Branch', function ($query) use ($kw) {
                        return $query->where(DB::raw('LOWER(kode)'), 'LIKE', '%' . strtolower($kw) . '%');
                    });
                });
            })
            ->filterColumn('item', function ($q, $kw) {
                $q->whereHas('Stock', function ($q) use ($kw) {
                    $q->whereHas('ProdukObat', function ($query) use ($kw) {
                        return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                    });
                    $q->orWhereHas('ItemNonObat', function ($query) use ($kw) {
                        return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                    });
                });
            })
            ->filterColumn('jenis_stock', function ($q, $kw) {
                $q->whereHas('Stock', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(jenis_stock)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->addColumn('harga_satuan', function ($data) {
                return number_format($data->harga_satuan);
            })
            ->addColumn('qty', function ($data) {
                return number_format($data->qty);
            })
            ->addColumn('total_harga', function ($data) {
                return number_format($data->total_harga);
            })
            ->addColumn('sisa_qty', function ($data) {
                return \App\Models\MutasiStock::where('stock_id', $data->id)->where('jenis', 'PENERIMAAN')->sum('qty_tersisa');
            })
            ->addColumn('jenis_stock', function ($data) {
                return $data->Stock->jenis_stock;
            })
            ->addColumn('jenis_mutasi', function ($data) {
                if ($data->jenis == 'PENERIMAAN') {
                    return '<i class="fa fa-arrow-up" style="color: green"></i>';
                } else {
                    return '<i class="fa fa-arrow-down" style="color: red"></i>';
                }
            })
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy != null ? $data->CreatedBy->name : null;
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'lokasi_cabang', 'jam_buka', 'jenis_mutasi'])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);
            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                Auth::user()->akses('create', null, true);

                // $input['id'] = $this->model->mutasiStock()->max('id') + 1;
                $input['kode'] = strtoupper($req->kode);
                $input['telpon'] = str_replace('_', '', $req->telpon);
                $input['open_time'] = strtoupper(str_replace('_', '', $req->open_time));
                $input['close_time'] = strtoupper(str_replace('_', '', $req->close_time));
                $input['open_holiday_time'] = strtoupper(str_replace('_', '', $req->open_holiday_time));
                $input['close_holiday_time'] = strtoupper(str_replace('_', '', $req->close_holiday_time));
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;
                $validator = Validator::make(
                    $input,
                    [
                        'kode'       => 'required|min:2|unique:mk_branch',
                    ],
                    [
                        'kode.unique'        => 'Kode sudah ada',
                        'kode.min'        => ':attribute minimal harus :min karakter.',
                    ]
                );
                if ($validator->fails()) {
                    return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
                }



                $this->model->mutasiStock()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['kode'] = strtoupper($req->kode);
                $input['telpon'] = str_replace('_', '', $req->telpon);
                $input['open_time'] = strtoupper(str_replace('_', '', $req->open_time));
                $input['close_time'] = strtoupper(str_replace('_', '', $req->close_time));
                $input['open_holiday_time'] = strtoupper(str_replace('_', '', $req->open_holiday_time));
                $input['close_holiday_time'] = strtoupper(str_replace('_', '', $req->close_holiday_time));
                $input['updated_by'] = me();

                $this->model->mutasiStock()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->mutasiStock()->where('id', $req->id)
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
        $data = $this->model->mutasiStock()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            try {
                $this->model->mutasiStock()->find($req->id)->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function mutasiStockExcel(Request $req)
    {
        return Excel::download(new MutasiStockExport($req), 'list_mutasi_stock.xlsx');
    }
}

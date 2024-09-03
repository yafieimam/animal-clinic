<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ItemNonObatController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_stock/item_non_obat/item_non_obat');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->itemNonObat()
            ->where(function ($q) use ($req) {
                if ($req->kategori != '') {
                    $q->where('kategori', $req->kategori);
                }

                if ($req->satuan_non_obat_id != '') {
                    $q->where('satuan_non_obat_id', $req->satuan_non_obat_id);
                }
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_stock/item_non_obat/action_button_item_non_obat', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('kategoriObat', function ($data) {
                return $data->KategoriObat != null ? $data->KategoriObat->name : "-";
            })
            ->addColumn('kategori', function ($data) {
                if ($data->kategori == 'DIJUAL BEBAS') {
                    return '<span class="badge badge-info">Tanpa Resep Dokter</span>';
                } else {
                    return '<span class="badge badge-info">Dengan Resep Dokter</span>';
                }
            })
            ->addColumn('satuanNonObat', function ($data) {
                return $data->SatuanNonObat != null ? $data->SatuanNonObat->kode : "-";
            })
            ->addColumn('harga', function ($data) {
                return number_format($data->harga);
            })
            ->rawColumns(['aksi', 'status', 'kategori'])
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
                $input['id'] = $this->model->itemNonObat()->max('id') + 1;
                $input['name'] = ucwords($req->name);
                $input['harga'] = convertNumber($req->harga);
                $input['description'] = ucwords($req->description);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;

                $validator = Validator::make(
                    $input,
                    [
                        'kode'       => 'required|unique:mo_produk_obat',
                    ],
                    [
                        'kode.unique'        => 'Kode sudah ada',
                    ]
                );


                if ($validator->fails()) {
                    return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
                }

                $this->model->itemNonObat()->create($input);
                generateStock();
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->name);
                $input['harga'] = convertNumber($req->harga);
                $input['description'] = ucwords($req->description);
                $input['updated_by'] = me();


                $this->model->itemNonObat()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'ITM-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->itemNonObat()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->itemNonObat()
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


        $index = str_pad($index, 4, '0', STR_PAD_LEFT);

        $kode = $kode . $index;

        return Response()->json(['status' => 1, 'kode' => $kode]);
    }


    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->itemNonObat()->where('id', $req->id)
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
        $data = $this->model->itemNonObat()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->itemNonObat()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SatuanNonObatController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_stock/satuan_non_obat/satuan_non_obat');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->satuanNonObat()->all();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_stock/satuan_non_obat/action_button_satuan_non_obat', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->rawColumns(['aksi', 'status'])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);


            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                $input['id'] = $this->model->satuanNonObat()->max('id') + 1;
                $input['kode'] = strtoupper($req->kode);
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;
                $validator = Validator::make(
                    $input,
                    [
                        'kode'       => 'required|unique:ms_satuan_non_obat',
                        'name'       => 'required|unique:ms_satuan_non_obat',
                    ],
                    [
                        'name.unique'        => 'Nama sudah ada',
                        'kode.unique'        => 'Kode sudah ada',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
                }

                Auth::user()->akses('create', null, true);



                $this->model->satuanNonObat()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->name);
                $input['kode'] = strtoupper($req->kode);
                $input['description'] = ucwords($req->description);
                $input['updated_by'] = me();


                $this->model->satuanNonObat()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->satuanNonObat()->where('id', $req->id)
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
        $data = $this->model->satuanNonObat()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->satuanNonObat()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class JabatanController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_klinik/jabatan/jabatan');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->jabatan()->all();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_klinik/jabatan/action_button_jabatan', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('icon', function ($data) {
                return '<i class="' . $data->icon . ' text-2xl"></i>';
            })
            ->addColumn('grouprole', function ($data) {
                return $data->Grouprole != null ? $data->Grouprole->name : "-";
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence'])
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
                $input['id'] = $this->model->jabatan()->max('id') + 1;
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;

                $validator = Validator::make(
                    $input,
                    [
                        'name'       => 'unique:mk_jabatan'
                    ],
                    [
                        'name.unique'        => 'Nama Bagian Sudah Ada',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
                }

                Auth::user()->akses('create', null, true);


                $this->model->jabatan()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['updated_by'] = me();


                $this->model->jabatan()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->jabatan()->where('id', $req->id)
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
        $data = $this->model->jabatan()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            try {
                $this->model->jabatan()->find($req->id)->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

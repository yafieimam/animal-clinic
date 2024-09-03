<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AnamnesaController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_klinik/anamnesa/anamnesa');
    }

    public function datatable(Request $req)
    {
        $data = $this->model
            ->anamnesa()
            ->where(function ($q) use ($req) {
                if ($req->poli_id != '') {
                    $q->where('poli_id', $req->poli_id);
                }
            })
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_klinik/anamnesa/action_button_anamnesa', compact('data'));
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
            ->addColumn('poli', function ($data) {
                return $data->poli != null ? $data->poli->name : '-';
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
                if ($req->poli_id == 'semua') {
                    $poli = $this->model->poli()
                        ->where('status', true)
                        ->get();

                    foreach ($poli as $key => $value) {
                        $anamnesa = $this->model->anamnesa()
                            ->where('name', ucwords($req->name))
                            ->where('poli_id', $value->id)
                            ->where('status', true)
                            ->first();

                        if (!$anamnesa) {
                            $idAnamnesa = $this->model->anamnesa()->max('id') + 1;
                            $this->model->anamnesa()
                                ->create([
                                    'id' => $idAnamnesa,
                                    'name' => ucwords($req->name),
                                    'poli_id' => $value->id,
                                    'description' => ucwords($req->description),
                                    'status' => true,
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);
                        }
                    }
                } else {
                    $input['id'] = $this->model->anamnesa()->max('id') + 1;
                    $input['name'] = ucwords($req->name);
                    $input['description'] = ucwords($req->description);
                    $input['created_by'] = me();
                    $input['updated_by'] = me();
                    $input['status'] = true;
                    $this->model->anamnesa()->create($input);
                }
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['updated_by'] = me();

                $this->model
                    ->anamnesa()
                    ->find($req->id)
                    ->update($input);

                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model
                ->anamnesa()
                ->where('id', $req->id)
                ->update([
                    'status' => $req->param,
                ]);
            return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }

    public function edit(Request $req)
    {
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model
            ->anamnesa()
            ->where('id', $req->id)
            ->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            if ($this->model->anamnesa()->find($req->id)->pendaftaranPasienAnamnesa->isEmpty()) {
                $this->model->anamnesa()->find($req->id)->delete();
                return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
            } else {
                return Response()->json(['status' => 2, 'message' => 'Anamnesa ini sudah digunakan, gunakan status disabled untuk menghilangkan data.']);
            }
            try {
                $this->model
                    ->anamnesa()
                    ->find($req->id)
                    ->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

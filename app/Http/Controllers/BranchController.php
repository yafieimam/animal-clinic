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

class BranchController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_klinik/branch/branch');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->branch()->all();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_klinik/branch/action_button_branch', compact('data'));
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
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('lokasi_cabang', function ($data) {
                return $data->alamat . '<br> No Telp:' . '+62' . ltrim($data->telpon, '0');
            })
            ->addColumn('jam_buka', function ($data) {
                return 'Buka Pukul<br>' . $data->open_time . ' s/d ' . $data->close_time . '<br><br>' . $data->hari_libur . '/Libur Nasional<br>' . $data->open_holiday_time . ' s/d ' . $data->close_holiday_time;
            })
            ->addColumn('telpon', function ($data) {
                return '+62' . ltrim($data->telpon, '0');
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'lokasi_cabang', 'jam_buka'])
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

                $input['id'] = $this->model->branch()->max('id') + 1;
                $input['kode'] = strtoupper($req->kode);
                $input['telpon'] = str_replace('_', '', $req->telpon);
                $input['open_time'] = strtoupper(str_replace('_', '', $req->open_time));
                $input['close_time'] = strtoupper(str_replace('_', '', $req->close_time));
                $input['open_holiday_time'] = strtoupper(str_replace('_', '', $req->open_holiday_time));
                $input['close_holiday_time'] = strtoupper(str_replace('_', '', $req->close_holiday_time));
                $input['hari_libur'] = $req->hari_libur;
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;
                $validator = Validator::make(
                    $input,
                    [
                        'kode' => 'required|min:2|unique:mk_branch',
                        'lokasi' => 'required|min:2|unique:mk_branch',
                    ],
                    [
                        'kode.unique' => 'Kode sudah ada',
                        'lokasi.unique' => 'Lokasi sudah ada',
                        'kode.min' => ':attribute minimal harus :min karakter.',
                    ],
                );

                if ($validator->fails()) {
                    return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
                }

                $this->model->branch()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['kode'] = strtoupper($req->kode);
                $input['telpon'] = str_replace('_', '', $req->telpon);
                $input['open_time'] = strtoupper(str_replace('_', '', $req->open_time));
                $input['close_time'] = strtoupper(str_replace('_', '', $req->close_time));
                $input['open_holiday_time'] = strtoupper(str_replace('_', '', $req->open_holiday_time));
                $input['close_holiday_time'] = strtoupper(str_replace('_', '', $req->close_holiday_time));
                $input['hari_libur'] = $req->hari_libur;
                $input['updated_by'] = me();

                $this->model
                    ->branch()
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
                ->branch()
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
            ->branch()
            ->where('id', $req->id)
            ->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            try {
                $this->model
                    ->branch()
                    ->find($req->id)
                    ->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

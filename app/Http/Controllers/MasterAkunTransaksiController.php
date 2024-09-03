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

class MasterAkunTransaksiController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('transaksi/master_akun_transaksi/master_akun_transaksi');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->masterAkunTransaksi()->all();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                $a = '';
                $b = '';
                $c = '';

                if (Auth::user()->akses('edit')) {
                $a = '<a href="javascript:;" class="btn btn-warning btn-round" style="color: black;width: 100px;" onclick="edit(\'' . $data->id . '\')"><i
                    class="fa fa-edit"></i> Ubah</a>  ';
                }

                if (Auth::user()->akses('delete')) {
                $b = '<a href="javascript:;" class="btn btn-danger btn-round" style="color: white;width: 100px;" onclick="hapus(\'' . $data->id . '\')"><i
                class="fa fa-trash"></i> Hapus</a>';
                }
                $c = '<a href="javascript:;" class="btn btn-info btn-round" style="color: white;width: 100px;" onclick="lihat(\'' . $data->id . '\')"><i
                class="fa fa-bars"></i> Lihat</a>';

                return '<div class="btn-group">' . $a . $b . $c . '</div>';
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
                $input['id'] = $this->model->masterAkunTransaksi()->max('id') + 1;
                $input['kode'] = strtoupper($req->kode);
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;

                Auth::user()->akses('create', null, true);



                $this->model->masterAkunTransaksi()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['kode'] = strtoupper($req->kode);
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['updated_by'] = me();


                $this->model->masterAkunTransaksi()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->masterAkunTransaksi()->where('id', $req->id)
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
        $data = $this->model->masterAkunTransaksi()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete',null,true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete',null,true);

            try {
                $this->model->masterAkunTransaksi()->find($req->id)->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

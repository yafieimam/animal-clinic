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

class SupplierController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_stock/supplier/supplier');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->supplier()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_stock/supplier/action_button_supplier', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('telpon', function ($data) {
                return  '+62' . ltrim($data->telpon, '0');
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'SUP-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->supplier()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->supplier()
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

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);

            $input['name'] = ucwords($req->name);
            $input['telpon'] = str_replace('_', '', $req->telpon);
            $input['nik'] = strtoupper($req->nik);
            $validator = Validator::make(
                $input,
                [
                    'kode'       => 'required|unique:ms_supplier' . ($req->id == null ? '' : ",kode,$req->id"),
                    'email'       => 'required|email|unique:ms_supplier' . ($req->id == null ? '' : ",email,$req->id"),
                    'telpon'     => 'min:10'
                ],
                [
                    'kode.unique'        => 'Kode sudah ada',
                    'email.unique'        => 'Email sudah ada',
                    'email.email'        => 'Format email salah',
                    'telpon.min'        => ':attribute minimal harus :min karakter.',
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
            }

            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                Auth::user()->akses('create', null, true);
                $input['id'] = $this->model->supplier()->max('id') + 1;
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;

                $this->model->supplier()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['updated_by'] = me();

                $this->model->supplier()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->supplier()->where('id', $req->id)
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
        $data = $this->model->supplier()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->supplier()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

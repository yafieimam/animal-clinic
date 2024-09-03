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


class RuleResepRacikanController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        if (Auth::user()->akses('edit')) {
            $edit = '<li>' .
                '<a href="javascript:;" onclick="edit(\'' . $data->id . '\')" class="dropdown-item text-info">' .
                '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah' .
                '</a>' .
                '</li>';
        }

        if (Auth::user()->akses('delete')) {
            $delete =  '<li>' .
                '<a href="javascript:;" onclick="hapus(\'' . $data->id . '\')" class="dropdown-item text-danger">' .
                '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus' .
                '</a>' .
                '</li>';
        }


        return '<div class="dropdown">' .
            '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
            '<span class="w-5 h-5 flex items-center justify-center">' .
            '<i class="fa fa-bars"></i>' .
            '</span>' .
            '</button>' .
            '<div class="dropdown-menu w-40 ">' .
            '<ul class="dropdown-content">' .
            '<li>' .
            '<a href="javascript:;" onclick="lihat(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
            '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' .
            '</a>' .
            '</li>' .
            $edit .
            $delete .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_obat/rule_resep_racikan/rule_resep_racikan');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->ruleResepRacikan()
            ->where(function ($q) use ($req) {
                if ($req->binatang_id != '') {
                    $q->where('binatang_id', $req->binatang_id);
                }

                if ($req->kategori_obat_id != '') {
                    $q->where('kategori_obat_id', $req->kategori_obat_id);
                }
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-info btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('kategoriObat', function ($data) {
                return $data->KategoriObat->name;
            })
            ->addColumn('harga', function ($data) {
                return number_format($data->harga);
            })
            ->rawColumns(['aksi', 'status'])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('create', null, true);

            Auth::user()->akses('edit', null, true);
            foreach ($req->kategori_obat_id as $i => $d) {
                if ($req->id[$i] == '' or $req->id[$i] == null) {
                    $id = $this->model->ruleResepRacikan()->max('id') + 1;
                    $this->model->ruleResepRacikan()
                        ->create([
                            'id'    => $id,
                            'kategori_obat_id'  => $req->kategori_obat_id[$i],
                            'min'   => $req->min[$i],
                            'symbol'    => $req->symbol[$i],
                            'max'   => $req->max[$i],
                            'satuan'    => $req->satuan[$i],
                            'harga' => convertNumber($req->harga[$i]),
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                } else {
                    $id = $req->id[$i];
                    $this->model->ruleResepRacikan()
                        ->find($id)
                        ->update([
                            'id'    => $id,
                            'kategori_obat_id'  => $req->kategori_obat_id[$i],
                            'min'   => $req->min[$i],
                            'symbol'    => $req->symbol[$i],
                            'max'   => $req->max[$i],
                            'satuan'    => $req->satuan[$i],
                            'harga' => convertNumber($req->harga[$i]),
                            'updated_by'    => me(),
                        ]);
                }

                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->ruleResepRacikan()->where('id', $req->id)
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
        $data = $this->model->ruleResepRacikan()->with(['KategoriObat', 'binatang'])->where('id', $req->id)->first();
        // return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->ruleResepRacikan()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

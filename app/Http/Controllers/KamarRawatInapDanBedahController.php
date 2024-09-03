<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;


class KamarRawatInapDanBedahController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_kamar/kamar_rawat_inap_dan_bedah/kamar_rawat_inap_dan_bedah');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->kamarRawatInapDanBedah()
            ->where(function ($q) use ($req) {
                if ($req->kategori_kamar_id != '') {
                    $q->where('kategori_kamar_id', $req->kategori_kamar_id);
                }

                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_kamar/kamar_rawat_inap_dan_bedah/action_button_kamar_rawat_inap_dan_bedah', compact('data'));
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
            ->addColumn('kategoriKamar', function ($data) {
                return $data->KategoriKamar != null ? $data->KategoriKamar->name : "-";
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->lokasi  : "-";
            })
            ->addColumn('tarif_per_hari', function ($data) {
                return number_format($data->tarif_per_hari);
            })
            ->addColumn('kapasitas', function ($data) {
                return $data->KamarRawatInapDanBedahDetail->where('status', 'In Use')->count() . ' / ' . $data->kapasitas;
            })
            ->addColumn('sisa_kamar', function ($data) {
                return $data->kapasitas - $data->KamarRawatInapDanBedahDetail->where('status', 'In Use')->count();
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('diskon', function ($data) {
                if ($data->diskon == 'true') {
                    return '<div class="py-1 px-2 rounded-full w-12 text-xs inline-block bg-success text-white cursor-pointer font-medium">Yes</div>';
                } else {
                    return '<div class="py-1 px-2 rounded-full w-12 text-xs inline-block bg-danger text-white cursor-pointer font-medium">No</div>';
                }
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'diskon'])
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
                $input['id'] = $this->model->kamarRawatInapDanBedah()->max('id') + 1;
                $input['name'] = ucwords($req->name);
                $input['kapasitas'] = convertNumber($req->kapasitas);
                $input['tarif_per_hari'] = convertNumber($req->tarif_per_hari);
                $input['description'] = ucwords($req->description);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;


                $this->model->kamarRawatInapDanBedah()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->name);
                $input['kapasitas'] = convertNumber($req->kapasitas);
                $input['tarif_per_hari'] = convertNumber($req->tarif_per_hari);
                $input['description'] = ucwords($req->description);
                $input['updated_by'] = me();


                $this->model->kamarRawatInapDanBedah()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->kamarRawatInapDanBedah()->where('id', $req->id)
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
        $data = $this->model->kamarRawatInapDanBedah()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            try {
                $this->model->kamarRawatInapDanBedah()->find($req->id)->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}

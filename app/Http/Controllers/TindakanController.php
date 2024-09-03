<?php

namespace App\Http\Controllers;

use App\Exports\BinatangExport;
use App\Http\Controllers\Controller;
use App\Models\Modeler;
use App\Models\Tindakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TindakanController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);

        // Tindakan::whereIn('binatang_id', [3, 4, 5, 6, 8, 11, 12, 13])->update(['hidden' => 't']);
        return view('management_klinik/tindakan/tindakan');
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
            $edit .
            $delete .
            '<li>' .
            '<a href="javascript:;" onclick="lihat(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
            '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' .
            '</a>' .
            '</li>' .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model->tindakan()
            ->where(function ($q) use ($req) {
                if ($req->poli_id != '') {
                    $q->where('poli_id', $req->poli_id);
                }

                if ($req->binatang_id != '') {
                    $q->where('binatang_id', $req->binatang_id);
                }
            })

            ->where(function ($q) use ($req) {
                $q->where('hidden', 'NOT LIKE', '%t%');
            })

            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
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
                return $data->Poli != null ? $data->Poli->name : "-";
            })
            ->addColumn('binatang', function ($data) {
                return $data->binatang != null ? $data->binatang->name : "-";
            })
            ->addColumn('tarif', function ($data) {
                return number_format($data->tarif);
            })
            ->addColumn('diskon', function ($data) {
                if ($data->diskon == 'true') {
                    return '<div class="py-1 px-2 rounded-full w-12 text-xs inline-block bg-success text-white cursor-pointer font-medium">Yes</div>';
                } else {
                    return '<div class="py-1 px-2 rounded-full w-12 text-xs inline-block bg-danger text-white cursor-pointer font-medium">No</div>';
                }
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
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

                if ($req->binatang_id == 'semua') {
                    $binatang = $this->model->binatang()
                        ->where('status', true)
                        ->get();
                    // foreach ($poli as $key => $value) {
                    //     $tindakan = $this->model->tindakan()
                    //         ->where('name', ucwords($req->name))
                    //         ->where('binatang_id', $req->binatang_id)
                    //         ->where('poli_id', $value->id)
                    //         ->where('status', true)
                    //         ->first();

                    //     if (!$tindakan) {
                    //         $idTindakan = $this->model->tindakan()->max('id') + 1;
                    //         $this->model->tindakan()
                    //             ->create([
                    //                 'id'    => $idTindakan,
                    //                 'name'  => ucwords($req->name),
                    //                 'binatang_id'   => $req->binatang_id,
                    //                 'poli_id'   => $value->id,
                    //                 'tarif' => convertNumber($req->tarif),
                    //                 'description'   => ucwords($req->description),
                    //                 'status'    => true,
                    //                 'created_by'    => me(),
                    //                 'updated_by'    => me(),
                    //                 'created_at'    => now(),
                    //                 'updated_at'    => now(),
                    //             ]);
                    //     }
                    // }

                    foreach ($binatang as $key => $value) {
                        $binatang = $this->model->tindakan()
                            ->where('name', ucwords($req->name))
                            ->where('binatang_id', $value->id)
                            ->where('status', true)
                            ->first();

                        if (!$binatang) {
                            $idTindakan = $this->model->tindakan()->max('id') + 1;
                            $this->model->tindakan()
                                ->create([
                                    'id'    => $idTindakan,
                                    'name'  => ucwords($req->name),
                                    'binatang_id'   => $value->id,
                                    // 'poli_id'   => $value->id,
                                    'tarif' => convertNumber($req->tarif),
                                    'description'   => ucwords($req->description),
                                    'diskon' => $req->diskon,
                                    'status'    => 'true',
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                    'created_at'    => now(),
                                    'updated_at'    => now(),
                                ]);
                        }
                    }
                } else {
                    $input['id'] = $this->model->tindakan()->max('id') + 1;
                    $input['name'] = ucwords($req->name);
                    $input['description'] = ucwords($req->description);
                    $input['tarif'] = convertNumber($req->tarif);
                    $input['created_by'] = me();
                    $input['updated_by'] = me();
                    $input['status'] = 'true';
                    $this->model->tindakan()->create($input);
                }

                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['tarif'] = convertNumber($req->tarif);
                $input['updated_by'] = me();


                $this->model->tindakan()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->tindakan()->where('id', $req->id)
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
        $data = $this->model->tindakan()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $check = $this->model->tindakan()->find($req->id);
            if ($check->rekamMedisRekomendasiTindakanBedah->count() != 0) {
                return Response()->json(['status' => 0, 'message' => 'Data ini tidak bisa dihapus']);
            }

            if ($check->rekamMedisTindakan->count() != 0) {
                return Response()->json(['status' => 0, 'message' => 'Data ini tidak bisa dihapus']);
            }

            try {
                $this->model->tindakan()->find($req->id)->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function bulkImport(Request $req)
    {
        return DB::transaction(function () use ($req) {

            $binatang = $this->model->binatang()
                ->where('status', true)
                ->get();
            foreach ($req->data as $i => $d) {
                if ($d[1] != '') {

                    if ($d[1] == 'semua') {
                        foreach ($binatang as $d1) {
                            $checkBinatang = $this->model->tindakan()
                                ->where(DB::raw('UPPER(name)'), strtoupper($d[0]))
                                ->where('binatang_id', $d1->id)
                                ->first();

                            if (!$checkBinatang) {
                                $this->model->tindakan()
                                    ->create([
                                        'id' => $this->model->tindakan()->max('id') + 1,
                                        'name'  => convertSlug($d[0]),
                                        'binatang_id'   => $d1->id,
                                        'tarif' => convertNumber($d[2]),
                                        'description'   => ucwords($d[3]),
                                        'status'    => $d[4] == 'ya' ? 'true' : 'false',
                                        'diskon' => 'false',
                                        'updated_by'    => me(),
                                        'created_at'    => now(),
                                        'updated_at'    => now(),
                                    ]);
                            } else {
                                $this->model->tindakan()
                                    ->where(DB::raw('UPPER(name)'), strtoupper($d[0]))
                                    ->where('binatang_id', $d1->id)
                                    ->update([
                                        'tarif' => convertNumber($d[2]),
                                        'description'   => ucwords($d[3]),
                                        'status'    => $d[4] == 'ya' ? 'true' : 'false',
                                        'updated_by'    => me(),
                                        'updated_at'    => now(),
                                    ]);
                            }
                        }
                    } else {
                        $search = $this->model->tindakan()
                            ->where(DB::raw('UPPER(name)'), strtoupper($d[0]))
                            ->where('binatang_id', $d[1])
                            ->first();

                        if ($search) {
                            $this->model->tindakan()
                                ->find($search->id)
                                ->update([
                                    'tarif' => convertNumber($d[2]),
                                    'description'   => ucwords($d[3]),
                                    'status'    => $d[4] == 'ya' ? 'true' : 'false',
                                    'diskon' => 'false',
                                    'updated_by'    => me(),
                                    'updated_at'    => now(),
                                ]);
                        } else {
                            $this->model->tindakan()
                                ->create([
                                    'id' => $this->model->tindakan()->max('id') + 1,
                                    'name'  => convertSlug($d[0]),
                                    'binatang_id'   => $d[1],
                                    'tarif' => convertNumber($d[2]),
                                    'description'   => ucwords($d[3]),
                                    'status'    => $d[4] == 'ya' ? 'true' : 'false',
                                    'diskon' => 'false',
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                    'created_at'    => now(),
                                    'updated_at'    => now(),
                                ]);
                        }
                    }
                }
            }
            return Response()->json(['status' => 1, 'message' => 'Berhasil bulk import']);
        });
    }

    public function binatangExcel()
    {
        return Excel::download(new BinatangExport, 'binatang_list.xlsx');
    }
}

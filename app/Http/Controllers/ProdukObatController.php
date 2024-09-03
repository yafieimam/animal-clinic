<?php

namespace App\Http\Controllers;

use App\Exports\KategoriObatExport;
use App\Exports\ProdukObatExport;
use App\Exports\SatuanObatExport;
use App\Exports\TypeObatExport;
use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProdukObatController extends Controller
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

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_obat/produk_obat/produk_obat');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->produkObat()
            ->where(function ($q) use ($req) {
                if ($req->kategori_obat_id != '') {
                    $q->where('kategori_obat_id', $req->kategori_obat_id);
                }

                if ($req->satuan_obat_id != '') {
                    $q->where('satuan_obat_id', $req->satuan_obat_id);
                }

                if ($req->type_obat_id != '') {
                    $q->where('type_obat_id', $req->type_obat_id);
                }
            })
            ->with([
                'KategoriObat',
                'SatuanObat',
                'TypeObat',
            ])
            ->get();

        return DataTables::of($data)
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
            ->addColumn('kategoriObat', function ($data) {
                return $data->KategoriObat != null ? $data->KategoriObat->name : "-";
            })
            ->addColumn('satuanObat', function ($data) {
                return $data->SatuanObat != null ? $data->SatuanObat->name : "-";
            })
            ->addColumn('typeObat', function ($data) {
                return $data->TypeObat != null ? $data->TypeObat->name : "-";
            })
            ->addColumn('harga', function ($data) {
                return 'Rp. ' . number_format($data->harga);
            })
            ->addColumn('diskon', function ($data) {
                if ($data->diskon == 'true') {
                    return '<div class="py-1 px-2 rounded-full w-12 text-xs inline-block bg-success text-white cursor-pointer font-medium">Yes</div>';
                } else {
                    return '<div class="py-1 px-2 rounded-full w-12 text-xs inline-block bg-danger text-white cursor-pointer font-medium">No</div>';
                }
            })
            ->rawColumns(['aksi', 'status', 'diskon'])
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
                $input['id'] = $this->model->produkObat()->max('id') + 1;
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['harga'] = convertNumber($req->harga);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;

                $validator = Validator::make(
                    $input,
                    [
                        'kode'       => 'required|unique:mo_produk_obat',
                        'name'       => 'required|unique:mo_produk_obat',
                    ],
                    [
                        'kode.unique'        => 'Kode sudah ada',
                        'name.unique'        => 'Nama Produk Obat sudah ada',
                    ]
                );


                if ($validator->fails()) {
                    return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
                }

                $this->model->produkObat()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->name);
                $input['description'] = ucwords($req->description);
                $input['harga'] = convertNumber($req->harga);
                $input['updated_by'] = me();


                $this->model->produkObat()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode =  'OBT' . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->produkObat()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->produkObat()
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
            $this->model->produkObat()->where('id', $req->id)
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
        $data = $this->model->produkObat()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            try {
                $this->model->produkObat()->find($req->id)->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function kategoriObatExcel(Request $req)
    {
        return Excel::download(new KategoriObatExport, 'kategori_obat_list.xlsx');
    }

    public function satuanObatExcel(Request $req)
    {
        return Excel::download(new SatuanObatExport, 'satuan_obat_list.xlsx');
    }

    public function typeObatExcel(Request $req)
    {
        return Excel::download(new TypeObatExport, 'type_obat_list.xlsx');
    }

    public function produkObatExcel(Request $req)
    {
        return Excel::download(new ProdukObatExport, 'produk_obat_list.xlsx');
    }

    public function bulkImport(Request $req)
    {
        return DB::transaction(function () use ($req) {
            foreach ($req->data as $i => $d) {
                $search = $this->model->produkObat()
                    ->where('kode', $d[0])
                    ->first();

                if (!$search) {
                    $search = $this->model->produkObat()
                        ->where('name', $d[1])
                        ->first();
                }

                if ($d[1] != '') {
                    if ($search) {
                        $this->model->produkObat()
                            ->find($search->id)
                            ->update([
                                'name' => $d[1],
                                'kategori_obat_id' => $d[2],
                                'type_obat_id' => $d[5],
                                'satuan_obat_id' => $d[3],
                                'harga' => convertNumber($d[4]),
                                'diskon' => strtolower($d[6]),
                                'updated_by' => me(),
                            ]);
                    } else {
                        $this->model->produkObat()
                            ->create([
                                'id' => $this->model->produkObat()->max('id') + 1,
                                'kode' => $this->generateKode($req)->getData()->kode,
                                'name' => $d[1],
                                'kategori_obat_id' => $d[2],
                                'type_obat_id' => $d[5],
                                'satuan_obat_id' => $d[3],
                                'status' => true,
                                'harga' => convertNumber($d[4]),
                                'diskon' => strtolower($d[6]),
                                'description' => '-',
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);
                    }
                }
            }
            return Response()->json(['status' => 1, 'message' => 'Berhasil bulk import']);
        });
    }
}

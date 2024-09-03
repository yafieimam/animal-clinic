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
use App\Exports\OwnerExport;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class OwnerController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_pasien/owner/owner');
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        if (Auth::user()->akses('edit')) {
            $edit = '<li>' . '<a href="javascript:;" onclick="edit(\'' . $data->id . '\')" class="dropdown-item text-info">' . '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah' . '</a>' . '</li>';
        }

        if (Auth::user()->akses('delete')) {
            $delete = '<li>' . '<a href="javascript:;" onclick="hapus(\'' . $data->id . '\')" class="dropdown-item text-danger">' . '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus' . '</a>' . '</li>';
        }

        $kartu = '<li>' . '<a href="javascript:;" onclick="lihatKartu(\'' . $data->kode . '\',\'' . ltrim($data->Branch->telpon, '0') . '\',\'' . $data->name . '\',\'' . $data->alamat . '\')" class="dropdown-item text-info">' . '<i class="fa-solid fa-id-card"></i>&nbsp;&nbsp;&nbsp;Lihat Kartu' . '</a>' . '</li>';

        $catatan = '<li>' . '<a href="javascript:;" onclick="tambahCatatan(\'' . $data->id . '\')" class="dropdown-item text-warning">' . '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Tambah Catatan' . '</a>' . '</li>';

        return '<div class="dropdown">' . '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' . '<span class="w-5 h-5 flex items-center justify-center">' . '<i class="fa fa-bars"></i>' . '</span>' . '</button>' . '<div class="dropdown-menu w-40 ">' . '<ul class="dropdown-content">' . '<li>' . '<a href="javascript:;" onclick="lihat(\'' . $data->id . '\')" class="dropdown-item text-warning">' . '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' . '</a>' . '</li>' . $edit . $delete . $kartu . $catatan . '</ul>' . '</div>' . '</div>';
    }

    public function datatableold(Request $req)
    {
        $kode = 'AMORE-' . 'XXX' . '-' . 'XXXXXXXX' . '-';
        $sub = strlen($kode) + 1;
        $data = $this->model
            ->owner()
            ->select('mp_owner.*', DB::raw("substring(kode,$sub) as kode_index"))
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }
            })
            ->where('name', '!=', 'Tanpa Owner')
            ->get();

        return Datatables::of($data)

            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
                // return view('management_pasien/owner/action_button_owner', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi : '-';
            })
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy ? $data->CreatedBy->name : '-';
            })
            ->addColumn('updated_by', function ($data) {
                return $data->UpdatedBy ? $data->UpdatedBy->name : '-';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'created_by', 'updated_by'])
            ->addIndexColumn()
            ->toJson();
    }

    public function datatable(Request $req)
    {
        $aColumns = ['kode', 'name', 'catatan', 'branch', 'email', 'telpon', 'alamat', 'komunitas', 'status', 'created_by', 'updated_by'];
        $limit = $req->input('length');
        $start = $req->input('start');
        $orderColumn = 'kode';
        $dir = $req->input('order.0.dir') ?? 'desc';
        $data = null;

        $kode = 'AMORE-' . 'XXX' . '-' . 'XXXXXXXX' . '-';
        $sub = strlen($kode) + 1;
        $data = $this->model
            ->owner()
            ->where('name', '!=', 'Tanpa Owner')
            ->select('mp_owner.*', DB::raw("substring(kode,$sub) as kode_index"))
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }
            });

        $totalData = $data->count();
        $totalFiltered = 0;
        $search = $req->input('search.value');
        // $postId = $req->input ( 'columns.1.search.value' );
        if (!empty($req->input('search.value'))) {
            $data = $data->where(function ($query) use ($search) {
                $query
                    ->where('kode', 'ilike', '%' . $search . '%')
                    ->orWhere('name', 'ilike', '%' . $search . '%')
                    ->orWhere('email', 'ilike', '%' . $search . '%')
                    ->orWhere('telpon', 'ilike', '%' . $search . '%')
                    ->orWhere('alamat', 'ilike', '%' . $search . '%')
                    ->orWhere('komunitas', 'ilike', '%' . $search . '%');
            });

            $data = $data->orWhereHas('Branch', function ($query) use ($search) {
                $query->where('kode', 'ilike', '%' . $search . '%')->orWhere('lokasi', 'ilike', '%' . $search . '%');
            });
            $data = $data->orWhereHas('CreatedBy', function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%');
            });
            $data = $data->orWhereHas('UpdatedBy', function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%');
            });

            $totalFiltered = $data->count();

            $data = $data->offset($start)->limit($limit);

            $requestAll = $req->all();
            if (isset($requestAll['order'][0]['column'])) {
                $orderColumn = $aColumns[$requestAll['order'][0]['column']];
            }
            $data = $data->orderBy($orderColumn, $dir);
        } else {
            $totalFiltered = $data->count();
            $data = $data->offset($start)->limit($limit);
            $requestAll = $req->all();
            if (isset($requestAll['order'][0]['column'])) {
                $orderColumn = $aColumns[$requestAll['order'][0]['column']];
            }

            $data = $data->orderBy($orderColumn, $dir);
        }
        $data = $data->get();
        $datas = [];
        if (!empty($data)) {
            foreach ($data as $key => $post) {
                $created_by = $post->CreatedBy ? $post->CreatedBy->name : '-';
                $updated_by = $post->UpdatedBy ? $post->UpdatedBy->name : '-';
                $branch = $post->Branch != null ? $post->Branch->kode . ' ' . $post->Branch->lokasi : '-';

                if ($post->status == true) {
                    $status = '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $post->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    $status = '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $post->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }

                $edit = '';
                $delete = '';
                if (Auth::user()->akses('edit')) {
                    $edit = '<li>' . '<a href="javascript:;" onclick="edit(\'' . $post->id . '\')" class="dropdown-item text-info">' . '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah' . '</a>' . '</li>';
                }

                if (Auth::user()->akses('delete')) {
                    $delete = '<li>' . '<a href="javascript:;" onclick="hapus(\'' . $post->id . '\')" class="dropdown-item text-danger">' . '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus' . '</a>' . '</li>';
                }

                $kartu = '<li>' . '<a href="javascript:;" onclick="lihatKartu(\'' . $post->kode . '\',\'' . ltrim($post->Branch->telpon, '0') . '\',\'' . $post->name . '\',\'' . $post->alamat . '\')" class="dropdown-item text-info">' . '<i class="fa-solid fa-id-card"></i>&nbsp;&nbsp;&nbsp;Lihat Kartu' . '</a>' . '</li>';

                $catatan = '<li>' . '<a href="javascript:;" onclick="tambahCatatan(\'' . $post->id . '\')" class="dropdown-item text-warning">' . '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Tambah Catatan' . '</a>' . '</li>';

                $aksi = '<div class="dropdown">' . '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' . '<span class="w-5 h-5 flex items-center justify-center">' . '<i class="fa fa-bars"></i>' . '</span>' . '</button>' . '<div class="dropdown-menu w-40 ">' . '<ul class="dropdown-content">' . '<li>' . '<a href="javascript:;" onclick="lihat(\'' . $post->id . '\')" class="dropdown-item text-warning">' . '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' . '</a>' . '</li>' . $edit . $delete . $kartu . $catatan . '</ul>' . '</div>' . '</div>';

                $nestedData['aksi'] = $aksi;
                $nestedData['status'] = $status;
                $nestedData['branch'] = $branch;
                $nestedData['name'] = $post->name;
                $nestedData['catatan'] = ($post->catatan == null) ? '-' : $post->catatan;
                $nestedData['telpon'] = $post->telpon;
                $nestedData['alamat'] = $post->alamat;
                $nestedData['komunitas'] = $post->komunitas;
                $nestedData['email'] = $post->email;
                $nestedData['kode'] = $post->kode;
                $nestedData['created_by'] = $created_by;
                $nestedData['updated_by'] = $updated_by;
                $nestedData['DT_RowIndex'] = $key + 1 + $start;

                $datas[] = $nestedData;
            }
        }

        $json_data = [
            'draw' => intval($req->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'start' => $start,
            'limit' => $limit,
            'order' => $orderColumn,
            'dir' => $dir,
            'search' => $search,
            'data' => $datas,
        ];

        return $json_data;
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('dmY');
        $branch = $this->model->branch()->find($req->branch_id);
        $kode = 'AMORE-' . $branch->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model
            ->owner()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            // ->where('kode', 'like', $kode . '%')
            ->take(10)
            ->first();

        $index = (int) $index->id + 1;
        $len = strlen($index);

        if ($len < 5) {
            $pad = 4;
        } else {
            $pad = $len;
        }

        $index = str_pad($index, $pad, '0', STR_PAD_LEFT);

        $kode = $kode . $index;

        return Response()->json(['status' => 1, 'kode' => $kode]);
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);

            $input['name'] = ucwords($req->name);
            $input['email'] = strtolower($req->email);
            $input['telpon'] = str_replace('-', '', $req->telpon);
            // $input['nik'] = strtoupper($req->nik);
            $validator = Validator::make(
                $input,
                [
                    'email' => 'required|email|unique:mp_owner' . ($req->id == null ? '' : ",email,$req->id"),
                    'telpon' => 'required|unique:mp_owner' . ($req->id == null ? '' : ",telpon,$req->id"),
                ],
                [
                    'telpon.unique' => 'Nomor telpon sudah terdaftar',
                    'email.unique' => 'Email sudah ada',
                    'email.email' => 'Format email salah',
                ],
            );

            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
            }

            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                Auth::user()->akses('create', null, true);
                $input['id'] = $this->model->owner()->max('id') + 1;
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;

                $this->model->owner()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['updated_by'] = me();

                $this->model
                    ->owner()
                    ->find($req->id)
                    ->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function storeCatatan(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('edit', null, true);

            $this->model
                ->owner()
                ->find($req->id_catatan)
                ->update([
                    'catatan' => $req->catatan,
                    'updated_by' => me()
                ]);
            return Response()->json(['status' => 1, 'message' => 'Catatan berhasil ditambahkan']);
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model
                ->owner()
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
            ->owner()
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
                    ->owner()
                    ->find($req->id)
                    ->delete();
            } catch (\Throwable $th) {
                return queryStatus($th->getCode());
            }
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    // Bondan
    public function OwnerExport(Request $req)
    {
        $aColumns = ['kode', 'name', 'branch', 'email', 'telpon', 'alamat', 'komunitas', 'status', 'created_by', 'updated_by'];
        $limit = -1;
        $start = 0;
        $orderColumn = 'kode';
        $dir = 'desc';
        $data = null;

        $kode = 'AMORE-' . 'XXX' . '-' . 'XXXXXXXX' . '-';
        $sub = strlen($kode) + 1;
        $data = $this->model
            ->owner()
            ->where('name', '!=', 'Tanpa Owner')
            ->select('mp_owner.*', DB::raw("substring(kode,$sub) as kode_index"))
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }
            });

        $totalData = $data->count();
        $totalFiltered = 0;
        $search = $req->input('search.value');
        // $postId = $req->input ( 'columns.1.search.value' );
        if (!empty($req->input('search.value'))) {
            $data = $data->where(function ($query) use ($search) {
                $query
                    ->where('kode', 'ilike', '%' . $search . '%')
                    ->orWhere('name', 'ilike', '%' . $search . '%')
                    ->orWhere('email', 'ilike', '%' . $search . '%')
                    ->orWhere('telpon', 'ilike', '%' . $search . '%')
                    ->orWhere('alamat', 'ilike', '%' . $search . '%')
                    ->orWhere('komunitas', 'ilike', '%' . $search . '%');
            });

            $data = $data->orWhereHas('Branch', function ($query) use ($search) {
                $query->where('kode', 'ilike', '%' . $search . '%')->orWhere('lokasi', 'ilike', '%' . $search . '%');
            });
            $data = $data->orWhereHas('CreatedBy', function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%');
            });
            $data = $data->orWhereHas('UpdatedBy', function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%');
            });

            $totalFiltered = $data->count();
            $data = $data->offset($start)->limit($limit);
            $requestAll = $req->all();
            if (isset($requestAll['order'][0]['column'])) {
                $orderColumn = $aColumns[$requestAll['order'][0]['column']];
            }
            $data = $data->orderBy($orderColumn, $dir);
        } else {
            $totalFiltered = $data->count();
            $data = $data->offset($start)->limit($limit);
            $requestAll = $req->all();
            if (isset($requestAll['order'][0]['column'])) {
                $orderColumn = $aColumns[$requestAll['order'][0]['column']];
            }

            $data = $data->orderBy($orderColumn, $dir);
        }
        $data = $data->get();
        $datas = [];
        if (!empty($data)) {
            foreach ($data as $key => $post) {
                $created_by = $post->CreatedBy ? $post->CreatedBy->name : '-';
                $updated_by = $post->UpdatedBy ? $post->UpdatedBy->name : '-';
                $branch = $post->Branch != null ? $post->Branch->kode . ' ' . $post->Branch->lokasi : '-';
                $status = $post->status ? 'Hidup' : 'Mati';
                // $nestedData['aksi'] = $aksi;
                $nestedData['status'] = $status;
                $nestedData['branch'] = $branch;
                $nestedData['name'] = $post->name;
                $nestedData['telpon'] = $post->telpon;
                $nestedData['alamat'] = $post->alamat;
                $nestedData['komunitas'] = $post->komunitas;
                $nestedData['email'] = $post->email;
                $nestedData['kode'] = $post->kode;
                $nestedData['created_by'] = $created_by;
                $nestedData['updated_by'] = $updated_by;
                $nestedData['no'] = $key + 1 + $start;
                array_push($datas, $nestedData);
            }
        }
        // dd($datas);
        return Excel::download(new OwnerExport($datas), 'owner.xlsx');
    }

    function regenerateCode(): JsonResponse
    {
        $kode = 'AMORE-' . 'XXX' . '-' . 'XXXXXXXX' . '-';
        $sub = strlen($kode) + 1;
        $data = $this->model->owner()
            ->where('kode', 'like', $kode . '%')
            ->where('max(cast(substring(kode,' . $sub . ') as INTEGER ))', '10000')
            ->get();
            
        dd($data);

        return response()->json([
            'status' => 1,
            'message' => 'Berhasil regenerate code',
        ]);
    }
}

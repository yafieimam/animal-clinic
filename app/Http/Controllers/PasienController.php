<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Image;
use Yajra\DataTables\Facades\DataTables;

class PasienController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        // $data = $this->model->pasien()->get();
        return view('management_pasien/pasien/pasien');
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        $rekamMedis = '<a href="javascript:;" onclick="lihatRekamMedis(\'' . $data->id . '\')" class="dropdown-item text-info">' .
            '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat Rekam Medis' .
            '</a>';
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

        $lihat =   '<li>' .
            '<a href="javascript:;" onclick="lihat(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
            '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' .
            '</a>' .
            '</li>';


        return "<div class='dropdown'>" .
            '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
            '<span class="w-5 h-5 flex items-center justify-center">' .
            '<i class="fa fa-bars"></i>' .
            '</span>' .
            '</button>' .
            '<div class="dropdown-menu w-52 ">' .
            '<ul class="dropdown-content">' .
            $rekamMedis .
            $edit .
            $delete .
            $lihat .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatableold(Request $req)
    {
        $data = $this->model->pasien()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if ($req->owner_id != '') {
                    $q->where('owner_id', $req->owner_id);
                }

                if ($req->binatang_id != '') {
                    $q->where('binatang_id', $req->binatang_id);
                }

                if ($req->ras_id != '') {
                    $q->where('ras_id', $req->ras_id);
                }
            })
            ->orderBy('created_at', 'ASC')
            ->limit(10)
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
            ->addColumn('icon', function ($data) {
                return '<i class="' . $data->icon . ' text-2xl"></i>';
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('owner', function ($data) {
                return $data->Owner != null ? $data->Owner->name  : "-";
            })
            ->addColumn('ras', function ($data) {
                return $data->Ras != null ? $data->Ras->name  : "-";
            })
            ->addColumn('binatang', function ($data) {
                return $data->binatang != null ? $data->binatang->name  : "-";
            })
            ->addColumn('date_of_birth', function ($data) {
                return date('d-M-Y', strtotime($data->date_of_birth));
            })
            ->addColumn('tanggal_awal_periksa', function ($data) {
                return date('d-M-Y', strtotime($data->tanggal_awal_periksa));
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('image', function ($data) {
                if ($data->image != null) {
                    return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->image . '" alt="No image">';
                } else {
                    return "-";
                }
            })
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy ? $data->CreatedBy->name : '-';
            })
            ->addColumn('updated_by', function ($data) {
                return $data->UpdatedBy ? $data->UpdatedBy->name : '-';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image', 'created_by', 'updated_by'])
            ->addIndexColumn()
            ->make(true);
    }

    public function datatable(Request $req)
    {
           
        $aColumns = [
            'image',
            'kode',
            'name',
            'branch_id',
            'binatang_id',
            'mk_ras',
            'sex',
            'owner_id',
            'ciri_khas',
            'date_of_birth',
            'berat',
            'tanggal_awal_periksa',
            'status',
            'created_by',
            'updated_by'
        ];
        $limit       = $req->input ( 'length' );
        $start       = $req->input ( 'start' );
        $orderColumn = "id";
        $dir         = $req->input ( 'order.0.dir' ) ?? 'desc';
        $data = null;
        $data = $this->model->pasien()->with('Owner');

        if ($req->branch_id != '') {
            $data = $data->where('branch_id', $req->branch_id);
        }
        if ($req->owner_id != '') {
            $data = $data->where('owner_id', $req->owner_id);
        }
        if ($req->binatang_id != '') {
            $data = $data->where('binatang_id', $req->binatang_id);
        }
        if ($req->ras_id != '') {
            $data = $data->where('ras_id', $req->ras_id);
        }
        $totalData = $data->count();
        $totalFiltered = 0;
        $search = $req->input('search.value');
        // $postId = $req->input ( 'columns.1.search.value' );
        if ( ! empty( $req->input('search.value') ) )
        {
            
            $data = $data->where(function($query) use ($search){
                $query
                ->where('kode', 'ilike', "%".$search."%")
                ->orWhere('name', 'ilike', "%".$search."%")
                ->orWhere('ciri_khas', 'ilike', "%".$search."%")
                ->orWhere('berat', 'ilike', "%".$search."%")
                ->orWhere('tinggi', 'ilike', "%".$search."%")
                ->orWhere('tanggal_awal_periksa', 'ilike', "%".$search."%")
                ->orWhere('suhu', 'ilike', "%".$search."%")
                ->orWhere('sex', 'ilike', "%".$search."%")
                ->orWhere('life_stage', 'ilike', "%".$search."%");
            });
            $data = $data->orWhereHas('Owner', function ($query) use ($search) {
                $query->where('name', 'ilike', "%".$search."%");
            });
            $data = $data->orWhereHas('CreatedBy', function ($query) use ($search) {
                $query->where('name', 'ilike', "%".$search."%");
            });
            $data = $data->orWhereHas('UpdatedBy', function ($query) use ($search) {
                $query->where('name', 'ilike', "%".$search."%");
            });
            $data = $data->orWhereHas('binatang', function ($query) use ($search) {
                $query->where('name', 'ilike', "%".$search."%");
            });

            $totalFiltered = $data->count();

            $data = $data->offset( $start )->limit( $limit );
            
            $requestAll = $req->all();
            if (isset($requestAll['order'][0]['column']))
            {
                $orderColumn = $aColumns[$requestAll['order'][0]['column']];
            }
            $data = $data->orderBy ( $orderColumn, $dir );
           
            
        }else{
            $totalFiltered = $data->count();
            $data = $data->offset( $start )->limit( $limit );
            $requestAll = $req->all();
            if (isset($requestAll['order'][0]['column']))
            {
                $orderColumn = $aColumns[$requestAll['order'][0]['column']];
            }
          
            $data = $data->orderBy ( $orderColumn, $dir );
          
        }
        $data = $data->get();
        $datas = [];
        if ( ! empty( $data ) )
        {
            foreach ( $data as $key  =>  $post )
            {
                // return  $post->Owner;
                if ($post->status == true) {
                    $status = '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $post->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    $status = '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $post->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
                $icon = '<i class="' . $post->icon . ' text-2xl"></i>';
                $branch = $post->Branch != null ? $post->Branch->kode . ' ' . $post->Branch->lokasi  : "-";
                $owner = $post->Owner != null ? $post->Owner->name  : "-";
                $ras = $post->Ras != null ? $post->Ras->name  : "-";
                $binatang = $post->binatang != null ? $post->binatang->name  : "-";
                $date_of_birth = date('d-M-Y', strtotime($post->date_of_birth));
                $tanggal_awal_periksa = date('d-M-Y', strtotime($post->tanggal_awal_periksa));
                $sequence = '<input type="number" value="' . $post->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $post->id . '\',this)">';
                if ($post->image != null) {
                    $image = '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $post->image . '" alt="No image">';
                } else {
                    $image = "-";
                }
                $created_by = $post->CreatedBy ? $post->CreatedBy->name : '-';
                $updated_by = $post->UpdatedBy ? $post->UpdatedBy->name : '-';

                $edit = '';
                $delete = '';
                $rekamMedis = '<a href="javascript:;" onclick="lihatRekamMedis(\'' . $post->id . '\')" class="dropdown-item text-info">' .
                    '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat Rekam Medis' .
                    '</a>';
                if (Auth::user()->akses('edit')) {
                    $edit = '<li>' .
                        '<a href="javascript:;" onclick="edit(\'' . $post->id . '\')" class="dropdown-item text-info">' .
                        '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah' .
                        '</a>' .
                        '</li>';
                }
        
                if (Auth::user()->akses('delete')) {
                    $delete =  '<li>' .
                        '<a href="javascript:;" onclick="hapus(\'' . $post->id . '\')" class="dropdown-item text-danger">' .
                        '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus' .
                        '</a>' .
                        '</li>';
                }
        
                $lihat =   '<li>' .
                    '<a href="javascript:;" onclick="lihat(\'' . $post->id . '\')" class="dropdown-item text-warning">' .
                    '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' .
                    '</a>' .
                    '</li>';
        
        
                $aksi =  "<div class='dropdown'>" .
                    '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
                    '<span class="w-5 h-5 flex items-center justify-center">' .
                    '<i class="fa fa-bars"></i>' .
                    '</span>' .
                    '</button>' .
                    '<div class="dropdown-menu w-52 ">' .
                    '<ul class="dropdown-content">' .
                    $rekamMedis .
                    $edit .
                    $delete .
                    $lihat .
                    '</ul>' .
                    '</div>' .
                    '</div>';

                $nestedData['aksi'] = $aksi;
                $nestedData['status'] = $status;
                $nestedData['icon'] = $icon;
                $nestedData['branch'] = $branch;
                $nestedData['owner'] = $owner;
                $nestedData['ras'] = $ras;
                $nestedData['binatang'] = $binatang;
                $nestedData['date_of_birth'] = $date_of_birth;
                $nestedData['tanggal_awal_periksa'] = $tanggal_awal_periksa;
                $nestedData['sequence'] = $sequence;
                $nestedData['image'] = $image;
                $nestedData['created_by'] = $created_by;
                $nestedData['updated_by'] = $updated_by;
                $nestedData['DT_RowIndex'] = $key + 1 + $start;
                $nestedData['name'] = $post->name;
                $nestedData['kode'] = $post->kode;
                $nestedData['berat'] = $post->berat;
                $nestedData['sex'] = $post->sex;
                $nestedData['ciri_khas'] = $post->ciri_khas;
                $datas[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval ( $req->input ( 'draw' ) ),
            "recordsTotal"    => intval ( $totalData ),
            "recordsFiltered" => intval ( $totalFiltered ),
            'start'           => $start,
            'limit'           => $limit,
            "order"             => $orderColumn,
            'dir'             => $dir,
            'search'            => $search,
            "data"            => $datas,
        );
           
        
        return  $json_data;

    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('dmY');
        $branch = $this->model->branch()->find($req->branch_id);
        $binatang = $this->model->binatang()->find($req->binatang_id);
        $kode = $binatang->kode . '-' . $tanggal . '-';
        $kode = $binatang->kode . '-' . $branch->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->pasien()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->pasien()
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

    public function getRekamMedis(Request $req)
    {
        $data = $this->model->Pasien()
            ->find($req->id);

        $rekamMedis = $this->model->rekamMedisPasien()
            ->where('pasien_id', $data->id)
            ->get();

        return view('management_pasien/pasien/template_data', compact('data', 'rekamMedis'));
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);
            unset($input['image']);

            $input['name'] = ucwords(strtolower($req->name));
            $validator = Validator::make(
                $input,
                [
                    'kode'       => 'required|unique:mp_pasien' . ($req->id == null ? '' : ",kode,$req->id"),
                ],
                [
                    'kode.unique'        => 'Kode sudah ada',
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
            }

            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                Auth::user()->akses('create', null, true);
                $input['id'] = $this->model->pasien()->max('id') + 1;
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['tanggal_awal_periksa'] = now();
                $input['sex'] = strtoupper($req->sex);
                $input['status'] = true;

                $file = $req->file('image');

                if ($file != null) {
                    $path = 'image/pasien';
                    $id =  Str::uuid($input['id'])->toString();
                    $name = $id . '.' . $file->getClientOriginalExtension();
                    $foto = $path . '/' . $name;
                    if (is_file($foto)) {
                        unlink($foto);
                    }

                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }

                    $img = Image::make(file_get_contents($file))->encode($file->getClientOriginalExtension(), 12);
                    $img->save($foto);

                    $input['image'] = $foto;
                }


                $this->model->pasien()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['updated_by'] = me();
                $pasien  = $this->model->pasien()->find($req->id);
                $input['sex'] = strtoupper($req->sex);
                $file = $req->file('image');
                if ($file != null) {
                    if (file_exists(public_path() . '/' . $pasien->image) and $pasien->image != null) {
                        gc_collect_cycles();
                        unlink(public_path() . '/' . $pasien->image);
                    }

                    $path = 'image/pasien';
                    $id =  Str::uuid($input['id'])->toString();
                    $name = $id . '.' . $file->getClientOriginalExtension();
                    $foto = $path . '/' . $name;
                    if (is_file($foto)) {
                        unlink($foto);
                    }

                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }

                    $img = Image::make(file_get_contents($file))->encode($file->getClientOriginalExtension(), 12);
                    $img->save($foto);

                    $input['image'] = $foto;
                }

                $this->model->pasien()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->pasien()->where('id', $req->id)
                ->update([
                    'status' => $req->param
                ]);
            return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }

    public function edit(Request $req)
    {
        Auth::user()->akses('edit', null, true);
        $data = $this->model->pasien()->with(['ras'])->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);

            if ($this->model->pasien()->find($req->id)->PendaftaranPasien->isEmpty()) {
                $this->model->pasien()->find($req->id)->delete();
                return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
            } else {
                return Response()->json(['status' => 2, 'message' => 'Pasien ini sudah pernah mendaftar, tidak bisa menghapus pasien']);
            }
        });
    }

    public function select2(Request $req)
    {
        switch ($req->param) {
            case 'ras_id':
                return $this->model->ras()
                    ->select('id', DB::raw("name as text"), 'mk_ras.*')
                    ->where('status', true)
                    ->where('binatang_id', $req->binatang_id)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            default:
                # code...
                break;
        }
    }
}
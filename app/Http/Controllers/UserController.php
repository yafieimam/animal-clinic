<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_karyawan/user/user');
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

        $lihat =  '<li>' .
            '<a href="javascript:;" onclick="lihat(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
            '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' .
            '</a>' .
            '</li>';

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
            $lihat .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model
            ->user()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if ($req->role_id != '') {
                    $q->where('role_id', $req->role_id);
                }
            })
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
            ->addColumn('role', function ($data) {
                return $data->Role != null ? $data->Role->name : '-';
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->name . ' ' . $data->Branch->lokasi : '-';
            })
            ->addColumn('karyawan', function ($data) {
                return $data->Karyawan != null ? '<button class="btn btn-primary">' . $data->Karyawan->name . '</button>' : '-';
            })
            ->addColumn('image', function ($data) {
                if ($data->image != null) {
                    return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->image . '">';
                } else {
                    return '<img style="width:100px;height:100px;object-fit:cover" src="' . url(asset('dist/images/amoreboxy.svg')) . '">';
                }
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image', 'karyawan'])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);
            unset($input['image']);

            $input['name'] = ucwords($req->name);
            $input['nama_panggilan'] = ucwords($req->nama_panggilan);
            $input['email'] = strtolower($req->email);
            $input['telpon'] = str_replace('-', '', $req->telpon);
            $input['nik'] = strtoupper($req->nik);

            $validator = Validator::make(
                $input,
                [
                    'username' => 'required|unique:users' . ($req->id == null ? '' : ",username,$req->id"),
                    'email' => 'required|email|unique:users' . ($req->id == null ? '' : ",email,$req->id"),
                    'password' => 'required|min:8',
                ],
                [
                    'username.unique' => 'Username sudah ada',
                    'email.email' => 'Format email salah',
                    'email.unique' => 'Email sudah ada',
                    'password.min' => ':attribute minimal adalah :min karakter.',
                ],
            );

            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
            }

            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                Auth::user()->akses('create', null, true);
                $input['id'] = $this->model->user()->max('id') + 1;
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;
                $input['password'] = bcrypt($req->password);
                $input['password_masked'] = $req->password;

                $file = $req->file('image');

                if ($file != null) {
                    $path = 'image/user';
                    $id = Str::uuid($input['id'])->toString();
                    $name = $id . '.' . str_replace('image/', '', $file->getMimeType());
                    $foto = $path . '/' . $name;
                    if (is_file($foto)) {
                        unlink($foto);
                    }

                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }

                    $img = Image::make(file_get_contents($file))->encode(str_replace('image/', '', $file->getMimeType()), 12);
                    $img->save($foto);

                    $input['image'] = $foto;
                }

                $this->model->user()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $user = $this->model->user()->find($req->id);
                $input['updated_by'] = me();
                $input['password'] = $req->password == null ? $user->password_masked : bcrypt($req->password);
                $input['password_masked'] = $req->password == null ? $user->password_masked : $req->password;
                $file = $req->file('image');
                if ($file != null) {
                    if (file_exists(public_path() . '/' . $user->image) and $user->image != null) {
                        gc_collect_cycles();
                        unlink(public_path() . '/' . $user->image);
                    }

                    $path = 'image/user';
                    $id = Str::uuid($input['id'])->toString();
                    $name = $id . '.' . str_replace('image/', '', $file->getMimeType());
                    $foto = $path . '/' . $name;
                    if (is_file($foto)) {
                        unlink($foto);
                    }

                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }

                    $img = Image::make(file_get_contents($file))->encode(str_replace('image/', '', $file->getMimeType()), 12);
                    $img->save($foto);

                    $input['image'] = $foto;
                }

                $this->model
                    ->user()
                    ->find($req->id)
                    ->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function updateProfile(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);
            unset($input['image']);

            $input['name'] = ucwords(Str::lower($req->name));
            $input['nama_panggilan'] = ucwords(Str::lower($req->nama_panggilan));
            $input['email'] = strtolower($req->email);
            $input['telpon'] = str_replace('-', '', $req->telpon);
            $user_id = Auth::user()->id;
            $validator = Validator::make(
                $input,
                [
                    'username' => 'required|unique:users' . ($user_id == null ? '' : ",username,$user_id"),
                    'email' => 'required|email|unique:users' . ($user_id == null ? '' : ",email,$user_id"),
                ],
                [
                    'username.unique' => 'Username sudah ada',
                    'email.email' => 'Format email salah',
                ],
            );

            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
            }

            $this->model
                ->user()
                ->find($user_id)
                ->update([
                    'name' => $input['name'],
                    'nama_panggilan' => $input['nama_panggilan'],
                    'username' => $req->username,
                    'email' => $req->email,
                ]);
            $this->model
                ->karyawan()
                ->find(Auth::user()->karyawan_id)
                ->update([
                    'name' => $input['name'],
                    'nama_panggilan' => $input['nama_panggilan'],
                    'email' => $input['email'],
                    'telpon' => $input['telpon'],
                    'status_pernikahan' => $req->status_pernikahan,
                    'updated_by' => me(),
                    'province_id' => $req->province_id,
                    'city_id' => $req->city_id,
                    'district_id' => $req->district_id,
                    'village_id' => $req->village_id,
                    'jenis_kelamin' => $req->jenis_kelamin,
                    'rt' => $req->rt,
                    'rw' => $req->rw,
                    'kode_pos' => $req->kode_pos,
                    'alamat' => $req->alamat,
                    'tanggal_lahir' => $req->tanggal_lahir,
                    'tempat_lahir' => $req->tempat_lahir,
                ]);

            return redirect()
                ->back()
                ->with([
                    'status' => 1,
                    'message' => 'Berhasil update profile',
                ]);
        });
    }

    public function updateImageProfile(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $file = $req->file('image');

            if ($file != null) {
                $path = 'image/user';
                $id = Str::uuid(Auth::user()->id)->toString();
                $name = $id . '.' . str_replace('image/', '', $file->getMimeType());
                $foto = $path . '/' . $name;

                if (is_file(Auth::user()->image)) {
                    unlink(Auth::user()->image);
                }

                if (is_file($foto)) {
                    unlink($foto);
                }

                if (!file_exists($path)) {
                    $oldmask = umask(0);
                    mkdir($path, 0777, true);
                    umask($oldmask);
                }

                $img = Image::make(file_get_contents($file))->encode(str_replace('image/', '', $file->getMimeType()), 12);
                $img->save($foto);
            } else {
                $foto = Auth::user()->image;
            }

            $this->model
                ->user()
                ->find(Auth::user()->id)
                ->update(['image' => $foto]);

            return Response()->json(['status' => 1, 'message' => 'Berhasil merubah foto profile']);
        });
    }

    public function updatePassword(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $validator = Validator::make(
                $req->all(),
                [
                    'password' => 'required|min:8',
                ],
                [
                    'password.min' => ':attribute minimal adalah :min karakter.',
                ],
            );

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $this->model
                ->user()
                ->find(Auth::user()->id)
                ->update(['password' => Hash::make($req->password)]);

            return redirect()
                ->back()
                ->with([
                    'status' => 1,
                    'message' => 'Berhasil merubah password',
                ]);
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model
                ->user()
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
            ->user()
            ->with(['Karyawan'])
            ->where('id', $req->id)
            ->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            if ($this->model->user()->RekamMedisPasien->count() == 0) {
                return Response()->json(['status' => 2, 'message' => 'User ini sudah punya relasi, gunakan status disabled untuk menonaktifkan data.']);
            }

            $this->model
                ->user()
                ->find($req->id)
                ->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function select2(Request $req)
    {
        switch ($req->param) {
            case 'karyawan_id':
                return $this->model
                    ->karyawan()
                    ->select('id', DB::raw('name as text'), 'mkr_karyawan.*')
                    ->where('status', true)
                    ->whereDoesntHave('User')
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw('UPPER(CONCAT(name))'), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            default:
                # code...
                break;
        }
    }

    public function editProfile()
    {
        return view('management_karyawan/user/edit_profile');
    }

    public function storeProfile(Request $req)
    {
        $id = Auth::user()->id;
        $validation = [
            'email' => 'required|email|unique:users' . ($id == null ? '' : ",email,$id"),
        ];

        $input['name'] = $req->name;
        $input['nama_panggilan'] = $req->nama_panggilan;
        $input['email'] = $req->email;

        if ($req->password) {
            $input['password'] = $req->password;
            $validation['password'] = 'required|min:8';
        }

        $validator = Validator::make(
            $req->all(),
            $validation,
            [
                'username.unique' => 'Username sudah ada',
                'email.email' => 'Format email salah',
                'email.unique' => 'Email sudah ada',
                'password.min' => ':attribute minimal adalah :min karakter.',
            ],
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }

        $file = $req->file('image');
        if ($file != null) {
            if (file_exists(public_path() . '/' . Auth::user()->image) and Auth::user()->image != null) {
                gc_collect_cycles();
                unlink(public_path() . '/' . Auth::user()->image);
            }

            $path = 'image/user';
            $id = Str::uuid(Auth::user()->id)->toString();
            $name = $id . '.' . str_replace('image/', '', $file->getMimeType());
            $foto = $path . '/' . $name;
            if (is_file($foto)) {
                unlink($foto);
            }

            if (!file_exists($path)) {
                $oldmask = umask(0);
                mkdir($path, 0777, true);
                umask($oldmask);
            }

            $img = Image::make(file_get_contents($file))->encode(str_replace('image/', '', $file->getMimeType()), 12);
            $img->save($foto);

            $input['image'] = $foto;
        }

        $input['password'] = bcrypt($req->password);
        $input['password_masked'] = $req->password;

        $this->model->user()
            ->find(Auth::user()->id)
            ->update($input);

        $this->model->karyawan()
            ->whereHas('user', function ($q) use ($req) {
                $q->where('id', Auth::user()->id);
            })
            ->update([
                'telpon' => $req->telpon,
                'name' => $req->name,
                'nama_panggilan' => $req->nama_panggilan,
                'email' => $req->email,
            ]);

        return redirect()
            ->back()
            ->with([
                'status' => 1,
                'message' => 'Berhasil update profile',
            ]);
    }
}

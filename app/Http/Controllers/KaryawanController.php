<?php

namespace App\Http\Controllers;

use App\Exports\BagianExport;
use App\Exports\BranchExport;
use App\Exports\DivisiExport;
use App\Exports\JabatanExport;
use App\Exports\KecamatanExport;
use App\Exports\KelurahanExport;
use App\Exports\KotaExport;
use App\Exports\ProvinsiExport;
use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_karyawan/karyawan/karyawan');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->karyawan()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }
                if ($req->divisi_id != '') {
                    $q->where('divisi_id', $req->divisi_id);
                }
                if ($req->bagian_id != '') {
                    $q->where('bagian_id', $req->bagian_id);
                }
                if ($req->jabatan_id != '') {
                    $q->where('jabatan_id', $req->jabatan_id);
                }
                if ($req->jenis_kelamin != '') {
                    $q->where('jenis_kelamin', $req->jenis_kelamin);
                }
                if ($req->status_pernikahan != '') {
                    $q->where('status_pernikahan', $req->status_pernikahan);
                }
            })
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_karyawan/karyawan/action_button_karyawan', compact('data'));
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
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('telpon', function ($data) {
                return  '+62' . ' ' . ltrim($data->telpon, '0');
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
            unset($input['file_ktp']);
            $input['name'] = ucwords($req->name);
            $input['nama_panggilan'] = ucwords($req->nama_panggilan);
            $input['email'] = strtolower($req->email);
            $input['telpon'] = str_replace('-', '', $req->telpon);
            $input['nik'] = strtoupper($req->nik);
            $input['nik'] = strtoupper($req->nik);
            $validator = Validator::make(
                $input,
                [
                    'email'       => 'required|email|unique:mkr_karyawan' . ($req->id == null ? '' : ",email,$req->id"),
                    'nik'       => 'required|unique:mkr_karyawan' . ($req->id == null ? '' : ",nik,$req->id"),
                    'telpon'       => 'required|unique:mkr_karyawan' . ($req->id == null ? '' : ",telpon,$req->id"),
                    // 'npwp'       => 'required|unique:mkr_karyawan' . ($req->id == null ? '' : ",npwp,$req->id"),
                    // 'bpjs'       => 'required|unique:mkr_karyawan' . ($req->id == null ? '' : ",bpjs,$req->id"),
                    // 'bpjs'       => 'required|unique:mkr_karyawan' . ($req->id == null ? '' : ",bpjs,$req->id"),
                    'jumlah_anak' => 'required|numeric|min:0',
                ],
                [
                    'nik.unique'        => 'NIK sudah ada',
                    'telpon.unique'        => 'No Telp sudah ada',
                    // 'bpjs.unique'        => 'No BPJS sudah ada',
                    // 'npwp.unique'        => 'No NPWP sudah ada',
                    'email.unique'        => 'Email sudah ada',
                    'email.email'        => 'Format email salah',
                    'jumlah_anak.min'        => 'Jumlah Anak Minimal 0',
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
            }

            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                Auth::user()->akses('create', null, true);
                $input['id'] = $this->model->karyawan()->max('id') + 1;
                $input['telpon'] = str_replace('_', '', $req->telpon);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;


                $file = $req->file('file_ktp');

                if ($file != null) {
                    $path = 'image/karyawan/ktp';
                    $id = Str::uuid($input['id'])->toString();
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

                    $input['file_ktp'] = $foto;
                }

                $this->model->karyawan()->create($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['telpon'] = str_replace('_', '', $req->telpon);
                $input['updated_by'] = me();
                $user = $this->model->karyawan()->find($req->id);
                $file = $req->file('file_ktp');
                if ($file != null) {
                    if (file_exists(public_path() . '/' . $user->file_ktp) and $user->file_ktp != null) {
                        gc_collect_cycles();
                        unlink(public_path() . '/' . $user->file_ktp);
                    }

                    $path = 'image/karyawan/ktp';
                    $id = Str::uuid($input['id'])->toString();
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

                    $input['file_ktp'] = $foto;
                }

                if (count($user->user) != 0) {
                    $this->model->user()
                        ->where('karyawan_id', $req->id)
                        ->update([
                            'name' => $req->name,
                            'nama_panggilan' => $req->nama_panggilan,
                        ]);
                }

                $this->model->karyawan()->find($req->id)->update($input);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->karyawan()->where('id', $req->id)
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
        $data = $this->model->karyawan()
            ->where('id', $req->id)
            ->with(['Provinsi', 'Kota', 'Kecamatan', 'Kelurahan', 'Branch'])
            ->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->karyawan()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function select2(Request $req)
    {
        switch ($req->param) {
            case 'city_id':
                return $this->model->kota()
                    ->select('id', DB::raw("name as text"))
                    ->where('province_id', $req->province_id)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            case 'district_id':

                return $this->model->kecamatan()
                    ->select('id', DB::raw("name as text"))
                    ->where('city_id', $req->city_id)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);

            case 'village_id':

                return $this->model->kelurahan()
                    ->select('id', DB::raw("name as text"))
                    ->where('district_id', $req->district_id)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);

            default:
                # code...
                break;
        }
    }

    public function bulkImport(Request $req)
    {
        return DB::transaction(function () use ($req) {

            foreach ($req->data as $i => $d) {
                $search = $this->model->karyawan()
                    ->where('nik', $d[0])
                    ->first();

                $checkEmail = $this->model->karyawan()
                    ->where('email', $d[12])
                    ->first();
                // dd($checkEmail);

                // dd($d, explode(',', $req->header));

                if ($d[1] != '') {
                    if ($search) {
                        $data = [];
                        foreach (explode(',', $req->header) as $key => $value) {
                            $data[$value] = $d[$key];
                        }
                        $data['updated_by'] = me();
                        $this->model->karyawan()
                            ->find($search->id)
                            ->update($data);
                    } else {
                        if ($checkEmail) {
                            DB::rollBack();
                            return Response()->json(['status' => 2, 'message' => 'Email ' . $d[12] . ' sudah terdaftar sebagai karyawan.']);
                        }
                        $data = [];
                        foreach (explode(',', $req->header) as $key => $value) {
                            $data[$value] = $d[$key];
                        }
                        $data['id'] = $this->model->karyawan()->max('id') + 1;
                        $data['created_by'] = me();
                        $data['updated_by'] = me();
                        $data['status'] = true;
                        $this->model->karyawan()
                            ->create($data);
                    }
                }
            }
            return Response()->json(['status' => 1, 'message' => 'Berhasil bulk import']);
        });
    }

    public function branchExcel()
    {
        return Excel::download(new BranchExport, 'branch_list.xlsx');
    }

    public function divisiExcel()
    {
        return Excel::download(new DivisiExport, 'divisi_list.xlsx');
    }

    public function bagianExcel()
    {
        return Excel::download(new BagianExport, 'bagian_list.xlsx');
    }

    public function jabatanExcel()
    {
        return Excel::download(new JabatanExport, 'jabatan_list.xlsx');
    }

    public function provinsiExcel()
    {
        return Excel::download(new ProvinsiExport, 'provinsi_list.xlsx');
    }

    public function kotaExcel()
    {
        return Excel::download(new KotaExport, 'kota_list.xlsx');
    }

    public function kecamatanExcel()
    {
        return Excel::download(new KecamatanExport, 'kecamatan_list.xlsx');
    }

    public function kelurahanExcel()
    {
        return Excel::download(new KelurahanExport, 'kelurahan_list.xlsx');
    }
}

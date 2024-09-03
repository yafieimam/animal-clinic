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

class JadwalDokterController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_karyawan/jadwal_dokter/jadwal_dokter');
    }

    public function datatable(Request $req)
    {

        $data = $this->model->jamKerja()
            ->orderBy('sequence', 'asc')
            ->get();

        return view('management_karyawan/jadwal_dokter/table_jadwal_dokter', compact('data', 'req'));
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
            '<li>' .
            '<a href="javascript:;" onclick="lihat(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
            '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' .
            '</a>' .
            '</li>' .
            $edit .
            $delete .
            $lihat .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatableData(Request $req)
    {
        try {
            $this->model->jadwalDokterDetail()
                ->whereHas('jadwalDokter', function ($q) {
                    $q->where('hari', '!=', convertDayToHari(Carbon::now()->format('l')));
                })
                ->update([
                    'status' => 'masuk'
                ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 1, 'message' => $th->getMessage()]);
        }
        
        $data = $this->model->jadwalDokter()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if ($req->poli_id != '') {
                    $q->where('poli_id', $req->poli_id);
                }

                if ($req->hari != '') {
                    $q->where('hari', $req->hari);
                }
            })
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            ->addColumn('poli', function ($data) {
                return $data->Poli != null ? $data->Poli->name  : "-";
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->lokasi  : "-";
            })
            ->addColumn('waktuJaga', function ($data) {
                return  $data->JamPertama->jam_awal . ':' . $data->JamPertama->menit_awal . ' s/d ' . $data->JamTerakhir->jam_awal . ':' . $data->JamTerakhir->menit_awal;
            })
            ->addColumn('hari', function ($data) {
                switch ($data->hari) {
                    case 'senin':
                    case 'selasa':
                    case 'rabu':
                    case 'kamis':
                    case 'jumat':
                        return '<span style="color:black">' . ucwords($data->hari) . '</span>';
                        break;
                    case 'sabtu':
                    case 'minggu':
                        return '<span style="color:red">' . ucwords($data->hari) . '</span>';
                        break;
                    default:
                        # code...
                        break;
                }
                return  ucwords($data->hari);
            })
            ->addColumn('dokter', function ($data) {
                $html = '<ul style="padding: 0;list-style-type: none;cursor: pointer">';
                foreach ($data->JadwalDokterDetail as $key => $value) {
                    $html .= '<li>' . $value->DataDokter->name . '</li>';
                }
                return $html .= '</ul>';
            })
            ->addColumn('status', function ($data) {
                $html = '<ul style="padding: 0;list-style-type: none;cursor: pointer">';
                foreach ($data->JadwalDokterDetail as $key => $value) {
                    $html .= '<li><select class="form-control w-full mt-1 status_dokter" name="status" data-dokter="'.$value->dokter.'" data-jadwal="'.$value->jadwal_dokter_id.'">';
                    $html .= '<option value="masuk" '.($value->status === 'masuk' ? 'selected' : '').'>Masuk</option>';
                    $html .= '<option value="izin" '.($value->status === 'izin' ? 'selected' : '').'>Izin</option>';
                    $html .= '<option value="sakit" '.($value->status === 'sakit' ? 'selected' : '').'>Sakit</option>';
                    $html .= '<option value="cuti" '.($value->status === 'cuti' ? 'selected' : '').'>Cuti</option>';
                    $html .= '<option value="libur" '.($value->status === 'libur' ? 'selected' : '').'>Libur</option>';
                    $html .= '</select></li>';
                }
                return $html .= '</ul>';
            })
            ->rawColumns(['aksi', 'role', 'dokter', 'branch', 'hari', 'status'])
            ->addIndexColumn()
            ->make(true);
    }


    public function datatableAddDokter(Request $req)
    {
        $selectedDokter = explode(',', $req->selectedDokter);
        $data = $this->model->user()
            ->has('karyawan')
            ->whereNotIn('id', $selectedDokter)
            ->where('branch_id', $req->branch_id)
            ->whereHas('Role', function ($q) use ($req) {
                $q->where('type_role', 'DOKTER');
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                $a = '';
                $a = '<input data-id="' . $data->id . '" type="checkbox" class="checkDokter">';

                return '<div class="btn-group">' . $a . '</div>';
            })
            ->addColumn('role', function ($data) {
                return $data->Role != null ? $data->Role->name  : "-";
            })
            ->addColumn('image', function ($data) {
                return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->image . '" alt="No image">';
            })
            ->rawColumns(['aksi', 'role', 'image'])
            ->addIndexColumn()
            ->make(true);
    }

    public function addDokter(Request $req)
    {
        $data = $this->model->user()
            ->whereIn('id', $req->selectingDokter)
            ->with(['role'])
            ->get();

        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('Ymd');
        $binatang = $this->model->binatang()->find($req->binatang_id);
        $kode = $binatang->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->jadwalDokter()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->jadwalDokter()
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
            unset($input['dokter']);

            if ($req->id == null or $req->id == 'null' or $req->id == '') {

                Auth::user()->akses('create', null, true);
                $input['id'] = $this->model->jadwalDokter()->max('id') + 1;
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = true;

                $this->model->jadwalDokter()->create($input);

                if (!isset($req->dokter)) {
                    DB::rollBack();

                    return Response()->json(['status' => 2, 'message' => 'Minimal harus memilih satu dokter']);
                }
                
                foreach ($req->dokter as $key => $value) {
                    $this->model->jadwalDokterDetail()
                        ->create([
                            'jadwal_dokter_id'  => $input['id'],
                            'id'    => $key + 1,
                            'dokter'    => $value,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                }

                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);
                $input['updated_by'] = me();
                $this->model->jadwalDokter()->find($req->id)->update($input);

                $this->model->jadwalDokterDetail()->find($req->id)->delete($input);

                if (!isset($req->dokter)) {
                    DB::rollBack();

                    return Response()->json(['status' => 2, 'message' => 'Minimal harus memilih satu dokter']);
                }
                foreach ($req->dokter as $key => $value) {
                    $this->model->jadwalDokterDetail()
                        ->create([
                            'jadwal_dokter_id'  => $input['id'],
                            'id'    => $key + 1,
                            'dokter'    => $value,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                }
                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->jadwalDokterDetail()->where('jadwal_dokter_id', $req->jadwal)
                ->where('dokter', $req->dokter)
                ->update([
                    'status' => $req->status,
                    'updated_by' => me(),
                    'updated_at' => now()
                ]);
                return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }

    public function edit(Request $req)
    {
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model->jadwalDokter()->with(['JadwalDokterDetail'])->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->jadwalDokter()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function select2(Request $req)
    {
        switch ($req->param) {
            case 'ras_id':
                return $this->model->ras()
                    ->select('id', DB::raw("name as text"), 'mk_ras.*')
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

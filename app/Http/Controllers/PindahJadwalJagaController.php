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
use Illuminate\Support\Str;
use Image;


class PindahJadwalJagaController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('management_karyawan/pindah_jadwal_jaga/pindah_jadwal_jaga');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->pindahJadwalJaga()
            ->where(function ($q) use ($req) {
                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if ($req->poli_id != '') {
                    $q->where('poli_id', $req->poli_id);
                }

                if ($req->dokter_peminta != '') {
                    $q->where('dokter_peminta', $req->dokter_peminta);
                }

                if ($req->dokter_diminta != '') {
                    $q->where('dokter_diminta', $req->dokter_diminta);
                }
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_karyawan/pindah_jadwal_jaga/action_button_pindah_jadwal_jaga', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('tanggal', function ($data) {
                return convertMonthToBulan($data->tanggal_awal) . ' ke ' . convertMonthToBulan($data->tanggal_tujuan);
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('DokterPeminta', function ($data) {
                return $data->DokterPeminta != null ? $data->DokterPeminta->name  : "-";
            })
            ->addColumn('DokterDiminta', function ($data) {
                return $data->DokterDiminta != null ? $data->DokterDiminta->name  : "-";
            })
            ->addColumn('jadwal', function ($data) {
                $a =  $data->JadwalDokterAwal != null ?  ucwords($data->JadwalDokterAwal->hari) . ' ' . $data->JadwalDokterAwal->JamPertama->jam_awal . ':' . $data->JadwalDokterAwal->JamPertama->menit_awal . ' s/d ' . $data->JadwalDokterAwal->JamTerakhir->jam_awal . ':' . $data->JadwalDokterAwal->JamTerakhir->menit_awal . ' ke ' : "-";
                $b =  $data->JadwalDokterTujuan != null ?  ucwords($data->JadwalDokterTujuan->hari) . ' ' . $data->JadwalDokterTujuan->JamPertama->jam_awal . ':' . $data->JadwalDokterTujuan->JamPertama->menit_awal . ' s/d ' . $data->JadwalDokterTujuan->JamTerakhir->jam_awal . ':' . $data->JadwalDokterTujuan->JamTerakhir->menit_awal  : "-";
                return $a . $b;
            })
            ->rawColumns(['aksi', 'status', 'jadwal'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('Ymd');
        $binatang = $this->model->binatang()->find($req->binatang_id);
        $kode = $binatang->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->pindahJadwalJaga()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->pindahJadwalJaga()
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

            if ($req->dokter_peminta == $req->dokter_diminta) {
                return Response()->json(['status' => 2, 'message' => 'Dokter peminta dan yang diminta tidak boleh sama.']);
            }
            // CHECK DOKTER PEMINTA TANGGAL AWAL
            $checkPindahJadwal = $this->model->pindahJadwalJaga()
                ->where('dokter_peminta', $req->dokter_diminta)
                ->where('dokter_diminta', $req->dokter_peminta)
                ->where('tanggal_awal', $req->tanggal_awal)
                ->first();

            if ($checkPindahJadwal) {
                $user = $this->model->user()->find($req->dokter_diminta);
                return Response()->json(['status' => 2, 'message' => 'Anda sudah diminta oleh ' . $user->name . ' untuk perpindahan jadwal jaga pada hari tersebut.']);
            }

            $checkPindahJadwal = $this->model->pindahJadwalJaga()
                ->where('dokter_diminta', $req->dokter_diminta)
                ->where('tanggal_awal', $req->tanggal_awal)
                ->first();

            if ($checkPindahJadwal) {
                $user = $this->model->user()->find($req->dokter_diminta);
                return Response()->json(['status' => 2, 'message' => 'Dokter ini sudah diminta pergantian jadwal pada hari tersebut.']);
            }
            // CHECK DOKTER DIMINTA TANGGAL TUJUAN
            $checkPindahJadwal = $this->model->pindahJadwalJaga()
                ->where('dokter_peminta', $req->dokter_diminta)
                ->where('dokter_diminta', $req->dokter_peminta)
                ->where('tanggal_tujuan', $req->tanggal_tujuan)
                ->first();

            if ($checkPindahJadwal) {
                $user = $this->model->user()->find($req->dokter_diminta);
                return Response()->json(['status' => 2, 'message' => 'Anda sudah diminta oleh ' . $user->name . ' untuk perpindahan jadwal jaga pada hari tersebut.']);
            }

            $checkPindahJadwal = $this->model->pindahJadwalJaga()
                ->where('dokter_diminta', $req->dokter_diminta)
                ->where('tanggal_tujuan', $req->tanggal_tujuan)
                ->first();

            if ($checkPindahJadwal) {
                $user = $this->model->user()->find($req->dokter_diminta);
                return Response()->json(['status' => 2, 'message' => 'Dokter ini sudah diminta pergantian jadwal pada hari tersebut.']);
            }

            $jadwalDokter = $this->model->jadwalDokter()
                ->find($req->jadwal_dokter_id);

            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                Auth::user()->akses('create', null, true);

                $checkPindahJadwal = $this->model->pindahJadwalJaga()
                    ->where('dokter_peminta', $req->dokter_peminta)
                    ->where('tanggal_awal', $req->tanggal_awal)
                    ->first();

                if ($checkPindahJadwal) {
                    $user = $this->model->user()->find($req->dokter_diminta);
                    return Response()->json(['status' => 2, 'message' => 'Anda sudah mengajukan pergantian jadwal jaga dengan ' . $checkPindahJadwal->DokterDiminta->name]);
                }

                $checkPindahJadwal = $this->model->pindahJadwalJaga()
                    ->where('dokter_peminta', $req->dokter_peminta)
                    ->where('tanggal_tujuan', $req->tanggal_tujuan)
                    ->first();

                if ($checkPindahJadwal) {
                    $user = $this->model->user()->find($req->dokter_diminta);
                    return Response()->json(['status' => 2, 'message' => 'Anda sudah mengajukan pergantian jadwal jaga dengan ' . $checkPindahJadwal->DokterDiminta->name]);
                }

                $id = $this->model->pindahJadwalJaga()->max('id') + 1;

                $this->model->pindahJadwalJaga()
                    ->create([
                        'id' => $id,
                        'tanggal_awal' => $req->tanggal_awal,
                        'tanggal_tujuan' => $req->tanggal_tujuan,
                        'branch_id' => isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id,
                        'dokter_peminta' => $req->dokter_peminta,
                        'dokter_diminta' => $req->dokter_diminta,
                        'jadwal_dokter_awal_id' => $req->jadwal_dokter_awal_id,
                        'jadwal_dokter_tujuan_id' => $req->jadwal_dokter_tujuan_id,
                        'status' => true,
                        'created_by' => me(),
                        'updated_by' => me(),
                    ]);
                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                Auth::user()->akses('edit', null, true);

                $checkPindahJadwal = $this->model->pindahJadwalJaga()
                    ->where('dokter_peminta', $req->dokter_peminta)
                    ->where('tanggal_awal', $req->tanggal_awal)
                    ->first();

                if ($checkPindahJadwal) {
                    $user = $this->model->user()->find($req->dokter_diminta);
                    return Response()->json(['status' => 2, 'message' => 'Anda sudah mengajukan pergantian jadwal jaga dengan ' . $checkPindahJadwal->DokterDiminta->name]);
                }

                $checkPindahJadwal = $this->model->pindahJadwalJaga()
                    ->where('dokter_peminta', $req->dokter_peminta)
                    ->where('tanggal_tujuan', $req->tanggal_tujuan)
                    ->first();

                if ($checkPindahJadwal) {
                    $user = $this->model->user()->find($req->dokter_diminta);
                    return Response()->json(['status' => 2, 'message' => 'Anda sudah mengajukan pergantian jadwal jaga dengan ' . $checkPindahJadwal->DokterDiminta->name]);
                }

                $this->model->pindahJadwalJaga()
                    ->find($req->id)
                    ->update([
                        'tanggal_awal' => $req->tanggal_awal,
                        'tanggal_tujuan' => $req->tanggal_tujuan,
                        'branch_id' => isset($req->branch_id) ? $req->$req->branch_id : Auth::user()->branch_id,
                        'dokter_peminta' => $req->dokter_peminta,
                        'dokter_diminta' => $req->dokter_diminta,
                        'jadwal_dokter_awal_id' => $req->jadwal_dokter_awal_id,
                        'jadwal_dokter_tujuan_id' => $req->jadwal_dokter_tujuan_id,
                        'updated_by' => me(),
                    ]);
                    return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->pindahJadwalJaga()->where('id', $req->id)
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
        $data = $this->model->pindahJadwalJaga()
            ->with([
                'JadwalDokterAwal' => function ($q) {
                    $q->with(['JamPertama', 'JamTerakhir']);
                },
                'JadwalDokterTujuan' => function ($q) {
                    $q->with(['JamPertama', 'JamTerakhir']);
                },
                'DokterPeminta',
                'DokterDiminta',
                'Branch',
                'Poli'
            ])
            ->find($req->id);
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->pindahJadwalJaga()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function select2(Request $req)
    {
        switch ($req->param) {
            case 'jadwal_dokter_id':
                return $this->model->jadwalDokter()
                    ->with(['JamPertama', 'JamTerakhir', 'Poli', 'Branch'])
                    ->where('hari', $req->hari)
                    ->where('branch_id', $req->branch_id == null ? Auth::user()->branch_id : $req->branch_id)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(hari)"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->whereHas('JadwalDokterDetail', function ($q) use ($req) {
                        $q->where('dokter', $req->dokter);
                    })
                    ->paginate(10);
            default:
                # code...
                break;
        }
    }

    public function generateHari(Request $req)
    {
        $data = convertDayToHari(carbon::parse($req->tanggal)->format('l'));
        return Response()->json(['status' => 1, 'data' => $data]);
    }
}

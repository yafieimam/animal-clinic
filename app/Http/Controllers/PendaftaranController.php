<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use App\Models\User;
use App\Notifications\PendaftaranNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Image;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Barryvdh\DomPDF\Facade as PDF;


class PendaftaranController extends Controller
{
    public $model;
    public $notify;
    public function __construct()
    {
        $this->model  = new Modeler();
        $this->notify = new NotifyController();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('quick_menu/pendaftaran/pendaftaran');
    }

    public function datatable(Request $req)
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
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('management_pasien/pasien/action_button_pasien', compact('data'));
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-info btn-round btn-xs" onclick="gantiStatus(false,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
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
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('image', function ($data) {
                return '<img style="width:100px;height:100px" src="' . url('/') . '/' . $data->image . '" alt="No image">';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Request $req)
    {
        // $tanggal = Carbon::now()->format('Ymd');
        $branch = $this->model->branch()->find($req->branch_id);
        $kode = $branch != null ? $branch->kode . '-' : Auth::user()->Branch->kode . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->pendaftaran()
            ->selectRaw('max(substring(kode_pendaftaran,' . $sub . ')) as id')
            ->where('kode_pendaftaran', 'like', $kode . '%')
            ->where('branch_id', $branch != null ? $branch->id  : Auth::user()->Branch->id)
            ->where('tanggal', dateStore())
            ->first();

        $collect = $this->model->pendaftaran()
            ->selectRaw('substring(kode_pendaftaran,' . $sub . ') as id')
            ->where('tanggal', dateStore())
            ->where('branch_id', $branch != null ? $branch->id  : Auth::user()->Branch->id)
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

    function num_cond($var1, $op, $var2)
    {

        switch ($op) {
            case "=":
                return $var1 == $var2;
            case "!=":
                return $var1 != $var2;
            case ">=":
                return $var1 >= $var2;
            case "<=":
                return $var1 <= $var2;
            case ">":
                return $var1 >  $var2;
            case "<":
                return $var1 <  $var2;
            default:
                return true;
        }
    }

    public function generateAge(Request $req)
    {
        $data = Carbon::parse($req->date_of_birth)->diff(Carbon::now())->format('%y tahun %m bulan %d hari');
        $year = Carbon::parse($req->date_of_birth)->diff(Carbon::now())->format('%y');
        $lifeStage = 0;;
        foreach (\App\Models\Pasien::$enumLifeStage as $key => $value) {
            if ($value['max'] == '0') {
                if ($this->num_cond($year, $value['operator'], $value['min'])) {
                    $lifeStage = $value['title'];;
                }
            } elseif ($value['min'] == '0') {
                if ($this->num_cond($year, $value['operator'], $value['max'])) {
                    $lifeStage = $value['title'];;
                }
            } else {
                if ($this->num_cond($year, '>=', $value['min']) && $this->num_cond($year, $value['operator'], $value['max'])) {
                    $lifeStage = $value['title'];;
                }
            }
        }
        return Response()->json(['status' => 1, 'message' => 'Berhasil menghitung umur', 'data' => $data, 'life_stage' => $lifeStage]);
    }

    public function generateKodeOwner(Request $req)
    {
        $tanggal = Carbon::now()->format('dmY');
        $branch = Auth::user()->branch;
        $kode = 'AMORE-' . $branch->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->owner()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            // ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->owner()
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

    public function generateKodePasien($value)
    {
        $tanggal = Carbon::now()->format('dmY');
        $branch = Auth::user()->branch;
        $binatang = $this->model->binatang()->find($value);
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



    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('view', null, true);
            $input = $req->all();

            if ($req->email == '' or $req->email == null) {
                $validData = [
                    'telpon' => 'required|unique:mp_owner' . ($req->id_owner == 'undefined' ? '' : ",telpon,$req->id_owner"),
                ];
                $validRule = [
                    'telpon.unique' => 'No Telp sudah ada',
                ];
            } else {
                $validData = [
                    'telpon' => 'required|unique:mp_owner' . ($req->id_owner == 'undefined' ? '' : ",telpon,$req->id_owner"),
                    'email' => 'unique:mp_owner' . ($req->id_owner == 'undefined' ? '' : ",email,$req->id_owner"),
                ];

                $validRule = [
                    'telpon.unique' => 'No Telp sudah ada',
                    'email.unique' => 'Email sudah ada',
                ];
            }

            $validator = Validator::make(
                $input,
                $validData,
                $validRule,
            );

            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
            }

            $kodePendaftaran = $this->generateKode($req)->getData()->kode;
            if ($req->id_owner == null or $req->id_owner == 'null' or $req->id_owner == 'undefined') {
                $checkOwner = null;
            } else {
                $checkOwner = $this->model->owner()
                    ->find($req->id_owner);
            }

            if ($checkOwner == null) {
                $idOwner = $this->model->owner()->max('id') + 1;
                $kodeOwner = $this->generateKodeOwner($req)->getData()->kode;
                $this->model->owner()
                    ->create([
                        'id'    => $idOwner,
                        'kode'  => $kodeOwner,
                        'name'  => $req->owner_id,
                        'branch_id' => isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id,
                        'email' => $req->email,
                        'telpon'    => str_replace('_', '', $req->telpon),
                        'alamat'    => $req->alamat,
                        'komunitas'    => $req->komunitas,
                        'status'    => true,
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);
                $namaOwner = $req->owner_id;
                $telpOwner = str_replace('_', '', $req->telpon);
            } else {
                $idOwner = $req->id_owner;
                $this->model->owner()
                    ->find($req->id_owner)
                    ->update([
                        'email' => $req->email,
                        'telpon' => str_replace('_', '', $req->telpon),
                        'alamat'    => $req->alamat,
                        'komunitas'    => $req->komunitas,
                        'updated_by'    => me(),
                    ]);

                $kodeOwner = $checkOwner->kode;
                $namaOwner = $checkOwner->name;
                $telpOwner = str_replace('_', '', $req->telpon);
            }

            $idPendaftaran = $this->model->pendaftaran()->max('id') + 1;



            $check =  $this->model->pendaftaran()
                ->where('tanggal', '<', dateStore())
                ->where('status', 'Waiting')
                ->whereHas('PendaftaranPasien', function ($q) {
                    $q->where('status', 'Sudah Diperiksa');
                })
                ->where('owner_id', $idOwner)
                ->first();

            if ($check) {
                DB::rollBack();
                return Response()->json(['status' => 2, 'message' => 'Owner sudah terdaftar dan sedang menunggu pemeriksaan.']);
            }

            $this->model->pendaftaran()
                ->where('tanggal', '<', dateStore())
                ->where('status', 'Waiting')
                ->whereDoesntHave('PendaftaranPasien', function ($q) {
                    $q->where('status', 'Sudah Diperiksa');
                })
                ->where('owner_id', $idOwner)
                ->delete();

            $this->model->pendaftaran()
                ->create([
                    'id'    => $idPendaftaran,
                    'kode_pendaftaran'  => $kodePendaftaran,
                    'tanggal'   => Carbon::now()->format('Y-m-d'),
                    'owner_id'  => $idOwner,
                    'branch_id' => isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id,
                    'catatan'   => $req->catatan,
                    'status'    => 'Waiting',
                    'status_pickup' => $req->status_pickup,
                    'status_owner'  => $req->tanpa_owner == 'on' ? true : false,
                    'request_dokter'    => $req->request_dokter,
                    'poli_id'   => $req->poli_id,
                    'created_by'    => me(),
                    'updated_by'    => me(),
                ]);

            foreach ($req->pasien_id as $key => $value) {
                if (is_numeric($value)) {
                    $checkPasien = $this->model->pasien()
                        ->find($value);
                } else {
                    $checkPasien = null;
                }

                if (!$checkPasien) {
                    $kode = $this->generateKodePasien($req->binatang_id[$key])->getData()->kode;
                    $file = $req->file('image_' . $value);
                    $idPasien = $this->model->pasien()->max('id') + 1;
                    if ($file != null) {
                        $path = 'image/pasien';
                        $id =  Str::uuid($idPasien)->toString();
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

                        $fileName = $foto;
                    } else {
                        $fileName = null;
                    }

                    $this->model->pasien()
                        ->create([
                            'id'    =>  $idPasien,
                            'kode'  =>  $kode,
                            'name'  =>  $value,
                            'binatang_id'   =>  $req->binatang_id[$key],
                            'ras_id'    =>  $req->ras_id[$key],
                            'sex'    =>  $req->sex[$key],
                            'owner_id'  =>  $idOwner,
                            'branch_id' =>  isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id,
                            'life_stage' =>  $req->life_stage[$key],
                            'ciri_khas' =>  $req->ciri_khas[$key],
                            'date_of_birth'  =>  $req->date_of_birth[$key],
                            'image' =>  $fileName,
                            'tanggal_awal_periksa'  =>  dateStore(),
                            'status'    =>  true,
                            'created_by'    =>  me(),
                            'updated_by'    =>  me(),
                        ]);

                    $namaPasien = $req->pasien_id;
                    $kodePasien = $kode;
                } else {
                    $file = $req->file('image_' . $value);
                    $idPasien = $value;

                    if ($file != null) {
                        if (file_exists(public_path() . '/' . $checkPasien->image) and $checkPasien->image != null) {
                            gc_collect_cycles();
                            unlink(public_path() . '/' . $checkPasien->image);
                        }

                        $path = 'image/pasien';
                        $id =  Str::uuid($idPasien)->toString();
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

                        $fileName = $foto;
                    } else {
                        $fileName = $checkPasien->image;
                    }
                    $this->model->pasien()
                        ->find($value)
                        ->update([
                            'name'  =>  $checkPasien->name,
                            'binatang_id'   =>  $req->binatang_id[$key],
                            'ras_id'    =>  $req->ras_id[$key],
                            'sex'    =>  $req->sex[$key],
                            'owner_id'  =>  $idOwner,
                            'life_stage' =>  $req->life_stage[$key],
                            'ciri_khas' =>  $req->ciri_khas[$key],
                            'date_of_birth'  =>  $req->date_of_birth[$key],
                            'image' =>  $fileName,
                            'tanggal_awal_periksa'  =>  dateStore(),
                            'status'    =>  true,
                            'updated_by'    =>  me(),
                        ]);

                    $namaPasien = $checkPasien->name;
                    $kodePasien = $checkPasien->kode;
                }

                $rekamMedisPasien = $this->model->rekamMedisPasien()
                    ->orderBy('created_at', 'ASC')
                    ->whereHas('KamarRawatInapDanBedahDetail', function ($q) {
                        $q->where('status', 'In Use');
                    })
                    ->where('pasien_id', $idPasien)
                    ->first();

                if ($rekamMedisPasien) {
                    DB::rollBack();
                    return Response()->json(['status' => 2, 'message' => 'Pasien sudah terdaftar sebagai pasien rawat inap.']);
                }

                $this->model->pendaftaran_pasien()
                    ->create([
                        'pendaftaran_id'    => $idPendaftaran,
                        'id'    => $key + 1,
                        'pasien_id' => $idPasien,
                        'lain_lain' => $req->lain_lain[$key],
                        'status'    => 'Belum Diperiksa',
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);


                if ($req->input('anamnesa_' . $key . '_id')) {
                    foreach ($req->input('anamnesa_' . $key . '_id') as $key1 => $value) {

                        $this->model->pendaftaran_pasien_anamnesa()
                            ->create([
                                'pendaftaran_id'    =>  $idPendaftaran,
                                'id' =>  $key1 + 1,
                                'pasien_id' =>  $idPasien,
                                'anamnesa_id'   =>  $value,
                                'ya'    =>  $req->input('anamnesa_pilihan_ya_' . $key)[$key1],
                                'tidak' =>  $req->input('anamnesa_pilihan_tidak_' . $key)[$key1],
                                'keterangan'    =>  $req->input('keterangan_anamnesa_' . $key)[$key1],
                                'created_by'    =>  me(),
                                'updated_by'    =>  me(),
                            ]);
                    }
                }
            }

            DB::commit();
            $this->notify->generateMonitoringPendaftaran($idPendaftaran, isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id);
            // $connector = new WindowsPrintConnector("POS-58");
            // $printer = new Printer($connector);
            // $printer->setPrintWidth(580);
            // $image = App::make('snappy.image.wrapper');
            // //To file
            // $html = view('quick_menu/pendaftaran/print', compact('namaPasien', 'kodePasien', 'namaOwner', 'telpOwner', 'kodePendaftaran'));
            // $image->loadHTML($html);
            // $name = 'myfile-' . strtotime(now()) . '.jpg';
            // $image->save($name);
            // // header('Content-Type: application/pdf');
            // // header('Content-Disposition: attachment; filename="file.pdf"');
            // // echo $snappy->getOutput('http://www.github.com');
            // // $name = 'apa-itu-anjing-pitbull.jpg';
            // try {
            //     $img = EscposImage::load(public_path($name));
            // } catch (Exception $e) {
            //     unlink(public_path($name));
            //     throw $e;
            // }

            // $printer->bitImage($img); // bitImage() seems to allow larger images than graphics() on the TM-T20. bitImageColumnFormat() is another option.
            // $printer->cut();
            // $printer->feed(3);
            // $printer->pulse();
            // $printer->close();
            // unlink(public_path($name));
            return Response()->json([
                'status' => 1,
                'message' => 'Berhasil mendaftarkan pasien',
                'id' => $idPendaftaran,
                'telpon' => Auth::user()->Branch->telpon,
                'kode' => $kodeOwner,
                'name' => $namaOwner,
                'alamat' => $req->alamat
            ]);
        });
    }

    public function update(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('view', null, true);
            $input = $req->all();
            if ($req->email == '' or $req->email == null) {
                $validData = [
                    'telpon' => 'required|unique:mp_owner' . ($req->id_owner == 'undefined' ? '' : ",telpon,$req->id_owner"),
                ];
                $validRule = [
                    'telpon.unique' => 'No Telepon sudah ada',
                ];
            } else {
                $validData = [
                    'telpon' => 'required|unique:mp_owner' . ($req->id_owner == 'undefined' ? '' : ",telpon,$req->id_owner"),
                    'email' => 'unique:mp_owner' . ($req->id_owner == 'undefined' ? '' : ",email,$req->id_owner"),
                ];

                $validRule = [
                    'telpon.unique' => 'No Telepon sudah ada',
                    'email.unique' => 'Email sudah ada',
                ];
            }

            $validator = Validator::make(
                $input,
                $validData,
                $validRule,
            );

            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), Response::HTTP_BAD_REQUEST);
            }

            if ($req->id_owner == null or $req->id_owner == 'null' or $req->id_owner == 'undefined') {
                $checkOwner = null;
            } else {
                $checkOwner = $this->model->owner()
                    ->find($req->id_owner);
            }

            if ($checkOwner == null) {
                $idOwner = $this->model->owner()->max('id') + 1;
                $kodeOwner = $this->generateKodeOwner($req)->getData()->kode;
                $this->model->owner()
                    ->create([
                        'id'    => $idOwner,
                        'kode'  => $kodeOwner,
                        'name'  => $req->owner_id,
                        'branch_id' => isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id,
                        'email' => $req->email,
                        'telpon'    => str_replace('_', '', $req->telpon),
                        'alamat'    => $req->alamat,
                        'komunitas'    => $req->komunitas,
                        'status'    => true,
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);
                $namaOwner = $req->owner_id;
                $telpOwner = str_replace('_', '', $req->telpon);
            } else {
                $idOwner = $req->id_owner;
                $this->model->owner()
                    ->find($req->id_owner)
                    ->update([
                        'email' => $req->email,
                        'telpon' => str_replace('_', '', $req->telpon),
                        'alamat'    => $req->alamat,
                        'komunitas'    => $req->komunitas,
                        'updated_by'    => me(),
                    ]);

                $kodeOwner = $checkOwner->kode;
                $namaOwner = $checkOwner->name;
                $telpOwner = str_replace('_', '', $req->telpon);
            }

            $idPendaftaran = $req->pendaftaran_id;

            $this->model->pendaftaran()
                ->find($req->pendaftaran_id)
                ->update([
                    'tanggal'   => Carbon::now()->format('Y-m-d'),
                    'owner_id'  => $idOwner,
                    'branch_id' => isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id,
                    'catatan'   => $req->catatan,
                    'status'    => 'Waiting',
                    'status_pickup' => $req->status_pickup,
                    'status_owner'  => $req->tanpa_owner == 'on' ? true : false,
                    'request_dokter'    => $req->request_dokter,
                    'poli_id'   => $req->poli_id,
                    'updated_by'    => me(),
                ]);

            $this->model->pendaftaran_pasien()->where('pendaftaran_id', $idPendaftaran)->delete();
            $this->model->pendaftaran_pasien_anamnesa()->where('pendaftaran_id', $idPendaftaran)->delete();
            foreach ($req->pasien_id as $key => $value) {
                if (is_numeric($value)) {
                    $checkPasien = $this->model->pasien()
                        ->find($value);
                } else {
                    $checkPasien = null;
                }

                // if ($key == 1) {
                //     dd($req->all());
                // }
                if (!$checkPasien) {
                    $kode = $this->generateKodePasien($req->binatang_id[$key])->getData()->kode;
                    $file = $req->file('image_' . $value);
                    $idPasien = $this->model->pasien()->max('id') + 1;
                    if ($file != null) {
                        $path = 'image/pasien';
                        $id =  Str::uuid($idPasien)->toString();
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

                        $fileName = $foto;
                    } else {
                        $fileName = null;
                    }

                    $this->model->pasien()
                        ->create([
                            'id'    =>  $idPasien,
                            'kode'  =>  $kode,
                            'name'  =>  $value,
                            'binatang_id'   =>  $req->binatang_id[$key],
                            'ras_id'    =>  $req->ras_id[$key],
                            'sex'    =>  $req->sex[$key],
                            'owner_id'  =>  $idOwner,
                            'branch_id' =>  isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id,
                            'life_stage' =>  $req->life_stage[$key],
                            'ciri_khas' =>  $req->ciri_khas[$key],
                            'date_of_birth'  =>  $req->date_of_birth[$key],
                            'image' =>  $fileName,
                            'tanggal_awal_periksa'  =>  dateStore(),
                            'status'    =>  true,
                            'created_by'    =>  me(),
                            'updated_by'    =>  me(),
                        ]);

                    $namaPasien = $req->pasien_id;
                    $kodePasien = $kode;
                } else {
                    $file = $req->file('image_' . $value);
                    $idPasien = $value;

                    if ($file != null) {
                        if (file_exists(public_path() . '/' . $checkPasien->image) and $checkPasien->image != null) {
                            gc_collect_cycles();
                            unlink(public_path() . '/' . $checkPasien->image);
                        }

                        $path = 'image/pasien';
                        $id =  Str::uuid($idPasien)->toString();
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

                        $fileName = $foto;
                    } else {
                        $fileName = $checkPasien->image;
                    }

                    $this->model->pasien()
                        ->find($value)
                        ->update([
                            'name'  =>  $checkPasien->name,
                            'binatang_id'   =>  $req->binatang_id[$key],
                            'ras_id'    =>  $req->ras_id[$key],
                            'sex'    =>  $req->sex[$key],
                            'owner_id'  =>  $idOwner,
                            'life_stage' =>  $req->life_stage[$key],
                            'ciri_khas' =>  $req->ciri_khas[$key],
                            'date_of_birth'  =>  $req->date_of_birth[$key],
                            'image' =>  $fileName,
                            'tanggal_awal_periksa'  =>  dateStore(),
                            'status'    =>  true,
                            'updated_by'    =>  me(),
                        ]);

                    $namaPasien = $checkPasien->name;
                    $kodePasien = $checkPasien->kode;
                }

                $rekamMedisPasien = $this->model->rekamMedisPasien()
                    ->orderBy('created_at', 'ASC')
                    ->whereHas('KamarRawatInapDanBedahDetail', function ($q) {
                        $q->where('status', 'In Use');
                    })
                    ->where('pasien_id', $idPasien)
                    ->first();

                if ($rekamMedisPasien) {
                    DB::rollBack();
                    return Response()->json(['status' => 2, 'message' => 'Pasien ' . $rekamMedisPasien->Pasien->name . '  sudah terdaftar sebagai pasien rawat inap.']);
                }

                $this->model->pendaftaran_pasien()
                    ->create([
                        'pendaftaran_id'    => $idPendaftaran,
                        'id'    => $key + 1,
                        'pasien_id' => $idPasien,
                        'lain_lain' => $req->lain_lain[$key],
                        'status'    => 'Belum Diperiksa',
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);

                if ($req->input('anamnesa_' . $key . '_id')) {
                    foreach ($req->input('anamnesa_' . $key . '_id') as $key1 => $value) {
                        $this->model->pendaftaran_pasien_anamnesa()
                            ->create([
                                'pendaftaran_id'    =>  $idPendaftaran,
                                'id' =>  $key1 + 1,
                                'pasien_id' =>  $idPasien,
                                'anamnesa_id'   =>  $value,
                                'ya'    =>  $req->input('anamnesa_pilihan_ya_' . $key)[$key1],
                                'tidak' =>  $req->input('anamnesa_pilihan_tidak_' . $key)[$key1],
                                'keterangan'    =>  $req->input('keterangan_anamnesa_' . $key)[$key1],
                                'created_by'    =>  me(),
                                'updated_by'    =>  me(),
                            ]);
                    }
                }
            }


            // $this->notify->generateMonitoringPendaftaran($idPendaftaran, isset($req->branch_id) ? $req->branch_id : Auth::user()->branch_id);
            // $connector = new WindowsPrintConnector("POS-58");
            // $printer = new Printer($connector);
            // $printer->setPrintWidth(580);
            // $image = App::make('snappy.image.wrapper');
            // //To file
            // $html = view('quick_menu/pendaftaran/print', compact('namaPasien', 'kodePasien', 'namaOwner', 'telpOwner', 'kodePendaftaran'));
            // $image->loadHTML($html);
            // $name = 'myfile-' . strtotime(now()) . '.jpg';
            // $image->save($name);
            // // header('Content-Type: application/pdf');
            // // header('Content-Disposition: attachment; filename="file.pdf"');
            // // echo $snappy->getOutput('http://www.github.com');
            // // $name = 'apa-itu-anjing-pitbull.jpg';
            // try {
            //     $img = EscposImage::load(public_path($name));
            // } catch (Exception $e) {
            //     unlink(public_path($name));
            //     throw $e;
            // }

            // $printer->bitImage($img); // bitImage() seems to allow larger images than graphics() on the TM-T20. bitImageColumnFormat() is another option.
            // $printer->cut();
            // $printer->feed(3);
            // $printer->pulse();
            // $printer->close();
            // unlink(public_path($name));
            return Response()->json(['status' => 1, 'message' => 'Data Berhasil Diubah', 'id' => $idPendaftaran, 'telpon' => Auth::user()->Branch->telpon, 'kode' => $kodeOwner]);
        });
    }

    public function print(Request $req)
    {
        $data = $this->model->pendaftaran()->findOrFail($req->id);
        $namaOwner = $data->Owner->name;
        $telpOwner = $data->Owner->telpon;
        $kodePendaftaran = $data->kode_pendaftaran;
        $pdf = PDF::loadview('quick_menu/pendaftaran/print', compact('namaOwner', 'telpOwner', 'kodePendaftaran', 'data'))->setPaper('a4', 'potrait');
        return $pdf->stream('ANTREAN PENDAFTARAN-' . $kodePendaftaran . '-' . carbon::now()->format('Y-m-d') . '.pdf');
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
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model->pendaftaran()->find($req->id);
        return view('quick_menu/pendaftaran/edit_pendaftaran', compact('data'));
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->pasien()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function select2(Request $req)
    {
        switch ($req->param) {
            case 'owner_id':
                return $this->model->owner()
                    ->select('id', DB::raw("name as text"), 'mp_owner.*')
                    ->where('status', true)
                    ->where(function ($q) use ($req) {
                        $q->orWhere(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            case 'kode_owner':
                return $this->model->owner()
                    ->select('id', DB::raw("kode as text"), 'mp_owner.*')
                    ->where('status', true)
                    ->where(function ($q) use ($req) {
                        $q->orWhere(DB::raw("UPPER(CONCAT(kode))"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            case 'pasien_id':
                return $this->model->pasien()
                    ->select('id', DB::raw("name as text"), 'mp_pasien.*')
                    ->where('status', true)
                    ->with(['ras', 'binatang', 'branch'])
                    ->whereDoesntHave('Pendaftaran', function ($q) use ($req) {
                        $q->where('qm_pendaftaran.status', 'Waiting');
                    })
                    ->where(function ($q) use ($req) {
                        if (is_numeric($req->owner_id)) {
                            $q->where('owner_id', $req->owner_id);
                        } else {
                            $q->where('owner_id', 0);
                        }
                        $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);

            case 'binatang_id':
                return $this->model->binatang()
                    ->select('id', DB::raw("concat(kode,' ',name) as text"), 'mk_binatang.*')
                    ->where('status', true)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(CONCAT(kode,' ',name))"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            default:
                break;
        }
    }

    public function getHewan(Request $req)
    {
        $data = $this->model->pasien()
            ->find($req->id);

        return Response::json(['status' => 1, 'data' => $data]);
    }

    public function gantiAnamnesa(Request $req)
    {
        $data = $this->model->anamnesa()
            ->where('poli_id', $req->poli_id)
            ->where('status', true)
            ->get();
        return view('quick_menu/pendaftaran/template_ganti_anamnesa', compact('data'));
    }
}

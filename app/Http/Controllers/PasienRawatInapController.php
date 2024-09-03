<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;

use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade as PDF;

class PasienRawatInapController extends Controller
{
    public $model;
    public $notify;
    public function __construct()
    {
        $this->model  = new Modeler();
        $this->notify  = new NotifyController();
    }
    public function index()
    {
        Auth::user()->akses('view', null, true);
        $kamar = $this->model->kamarRawatInapDanBedah()
            ->orderBy('created_at', 'ASC')
            ->where(function ($q) {
                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->withCount(['KamarRawatInapDanBedahDetail as kamar_terpakai' => function ($q) {
                $q->where('status', 'In Use');
                $q->whereHas('RekamMedisPasien', function ($q) {
                    $q->where('status_bedah', false);
                });
            }])
            ->whereHas('KamarRawatInapDanBedahDetail', function ($q) {
                $q->where('status', 'In Use');
            })
            ->get();

        $pasien = $this->model->rekamMedisPasien()
            ->orderBy('created_at', 'ASC')
            ->whereHas('KamarRawatInapDanBedahDetail', function ($q) {
                $q->where('status', 'In Use');
            })
            ->where(function ($q) {
                if (!Auth::user()->akses('global')) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->has('Pendaftaran')
            ->get();
        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('id', 1)
            // ->where('dokter', me())
            ->first();
        return view('quick_menu/rekam_medis/rekam_medis', compact('pasien', 'data', 'kamar'));
    }

    public function detail($id)
    {
        return view('quick_menu/pemeriksaan_pasien/pemeriksaan_pasien');
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
                return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->image . '" alt="No image">';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Pasien $req)
    {
        $tanggal = Carbon::now()->format('Ymd');
        $binatang = $this->model->binatang()->find($req->binatang_id);
        $kode = 'RM-' . $binatang->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->rekamMedisPasien()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->rekamMedisPasien()
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
        return $kode;
    }

    public function tambahResep(Request $req)
    {
        $rekamMedis = $this->model->rekamMedisPasien()->find($req->id);


        $produkObat = $this->model->produkObat()
            ->with([
                'StockFirst' => function ($q) use ($rekamMedis) {
                    $q->where('branch_id', $rekamMedis->Pendaftaran->branch_id);
                }
            ])
            ->where('status', true)
            ->get();

        return view('quick_menu/rekam_medis/template_resep', compact('req', 'produkObat'));
    }

    public function tambahRacikanChild(Request $req)
    {
        $rekamMedis = $this->model->rekamMedisPasien()->find($req->id);

        $produkObat = $this->model->produkObat()
            ->with([
                'StockFirst' => function ($q) use ($rekamMedis) {
                    $q->where('branch_id', $rekamMedis->Pendaftaran->branch_id);
                }
            ])
            ->where('status', true)
            ->get();
        return view('quick_menu/rekam_medis/template_racikan_child', compact('req', 'produkObat'));
    }

    public function getRekamMedis(Request $req)
    {
        $data = $this->model->rekamMedisPasien()->find($req->id);
        return view('quick_menu/rekam_medis/template_data', compact('data', 'req'));
    }

    public function addRekamMedisLogHistory($id, $text, $table, $refId)
    {
        $idRekamMedisLogHistory = $this->model->rekamMedisLogHistory()
            ->where('rekam_medis_pasien_id', $id)
            ->max('id') + 1;

        $this->model->rekamMedisLogHistory()
            ->create([
                'rekam_medis_pasien_id' => $id,
                'id'    => $idRekamMedisLogHistory,
                'description'   => $text,
                'table'   => $table,
                'ref_id'   => $refId,
                'created_by'    =>  me(),
                'updated_by'    => me(),
            ]);
        return true;
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            switch ($req->jenis) {
                case 'diagnosa':
                    $idRekamMedisDiagnosa = $this->model->rekamMedisDiagnosa()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->max('id') + 1;

                    $this->model->rekamMedisDiagnosa()
                        ->create([
                            'rekam_medis_pasien_id' => $req->id,
                            'id'    => $idRekamMedisDiagnosa,
                            'diagnosa'  => $req->diagnosa,
                            'resource' => 'Rawat Inap',
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                    $text = '<b>' . Auth::user()->name . '</b> menambahkan diagnosa ke pasien ini <b>' . $req->diagnosa . ' </b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_diagnosa', $idRekamMedisDiagnosa);
                    break;
                case 'catatan':
                    $idRekamMedisDiagnosa = $this->model->rekamMedisCatatan()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->max('id') + 1;

                    $this->model->rekamMedisCatatan()
                        ->create([
                            'rekam_medis_pasien_id' => $req->id,
                            'id'    => $idRekamMedisDiagnosa,
                            'resource' => 'Rawat Inap',
                            'catatan'  => $req->catatan,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                    $text = '<b>' . Auth::user()->name . '</b> menambahkan catatan <b>' . $req->catatan . '</b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_diagnosa', $idRekamMedisDiagnosa);
                    break;
                case 'treatment':
                    $idRekamMedisTreatment = $this->model->rekamMedisTreatment()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->max('id') + 1;
                    $this->model->rekamMedisTreatment()
                        ->create([
                            'rekam_medis_pasien_id' => $req->id,
                            'id'    => $idRekamMedisTreatment,
                            'treatment' => $req->treatment,
                            'tarif' => 0,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                    $text = '<b>' . Auth::user()->name . '</b> memberi treatment <b>' . $req->treatment . ' </b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_treatment', $idRekamMedisTreatment);
                    break;
                case 'tindakan':
                    $idRekamMedisTindakan = $this->model->rekamMedisTindakan()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->max('id') + 1;
                    $tindakan = $this->model->tindakan()->find($req->tindakan_id);

                    $this->model->rekamMedisTindakan()
                        ->create([
                            'rekam_medis_pasien_id' => $req->id,
                            'id'    => $idRekamMedisTindakan,
                            'tindakan_id' => $req->tindakan_id,
                            'tarif' => $tindakan->tarif,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                    $text = '<b>' . Auth::user()->name . '</b> melakukan tindakan <b>' . $tindakan->name . ' </b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', $idRekamMedisTindakan);
                    break;
                case 'resep':
                    foreach ($req->parent_resep as $i => $d) {
                        $resep = '';
                        $idRekamMedisResep = $this->model->rekamMedisResep()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->max('id') + 1;
                        if ($d == 'racikan') {
                            $this->model->rekamMedisResep()
                                ->create([
                                    'rekam_medis_pasien_id' => $req->id,
                                    'id' => $idRekamMedisResep,
                                    'kategori_obat_id' => $req->jenis_obat_racikan[$i],
                                    'jenis_obat' => $d,
                                    'status_pembuatan_obat' => 'Undone',
                                    'status_resep' => $req->status_resep,
                                    'harga_jual' => 0,
                                    'description' => $req->description_racikan[$i],
                                    'satuan_obat_id' => $req->satuan_racikan[$i],
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);

                            $kategori = $this->model->kategoriObat()->find($req->jenis_obat_racikan[$i]);
                            $resep = $kategori->name . ' ';

                            if ($req->input('racikan_produk_obat_' . $req->index_racikan[$i]) == null) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Minimal ada 1 obat setiap resep racikan.']);
                            }
                            foreach ($req->input('racikan_produk_obat_' . $req->index_racikan[$i]) as $i1 => $d1) {
                                $this->model->rekamMedisResepRacikan()
                                    ->create([
                                        'rekam_medis_pasien_id' => $req->id,
                                        'rekam_medis_resep_id'  => $idRekamMedisResep,
                                        'id'    => $i1 + 1,
                                        'produk_obat_id'    => $d1,
                                        'qty'   => $req->input('racikan_qty_' . $req->index_racikan[$i])[$i1],
                                        'description'   => $req->description_racikan[$i],
                                        'created_by'    => me(),
                                        'updated_by'    => me(),
                                    ]);
                            }
                        } elseif ($d == 'non-racikan') {
                            $this->model->rekamMedisResep()
                                ->create([
                                    'rekam_medis_pasien_id' => $req->id,
                                    'id' => $idRekamMedisResep,
                                    'produk_obat_id' => $req->produk_obat_non_racikan[$i],
                                    'status_pembuatan_obat' => 'Undone',
                                    'status_resep' => $req->status_resep,
                                    'jenis_obat' => $d,
                                    'qty' => $req->qty_non_racikan[$i],
                                    'harga_jual' => convertNumber($req->harga_non_racikan),
                                    'description' => $req->description_non_racikan[$i],
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);
                            $produkObat = $this->model->produkObat()->find($req->produk_obat_non_racikan[$i]);
                            $resep = $produkObat->name;
                        }
                        $text = '<b>' . Auth::user()->name . '</b> memberi kan resep <b>' . $resep . ' </b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_resep', $idRekamMedisResep);
                    }

                    if ($req->status_resep == 'Langsung') {
                        $this->notify->broadcastingRequestObat($req);
                    }
                    break;
                case 'hasil lab':
                    foreach ($req->hasil_lab as $i => $d) {
                        $idRekamMedisHasilLab = $this->model->rekamMedisHasilLab()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->max('id') + 1;

                        $file = $d;

                        $path = 'image/rekam_medis_hasil_lab';
                        $id =  Str::uuid($req->id . ($i + 1))->toString();
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

                        Storage::disk('public_uploads')->put($foto, file_get_contents($file));

                        $this->model->rekamMedisHasilLab()
                            ->create([
                                'rekam_medis_pasien_id' => $req->id,
                                'id'    => $idRekamMedisHasilLab,
                                'file'  => $foto,
                                'name'  => $file->getClientOriginalName(),
                                'created_by'    => me(),
                                'updated_by'    => me(),
                            ]);

                        $text = '<b>' . Auth::user()->name . '</b> menambahkan hasil lab ' . $file->getClientOriginalName();
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_hasil_lab', $idRekamMedisHasilLab);
                    }
                    break;
                case 'boleh_pulang':
                    $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->id)->first();
                    if (!$check) {
                        return Response()->json(['status' => 2, 'message' => 'Data corrupt, hubungi developer']);
                    }

                    $rekamMedisPasien = $this->model->kamarRawatInapDanBedahDetail()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->update([
                            'status' => 'Done',
                            'tanggal_keluar' => dateStore()
                        ]);


                    $this->model->rekamMedisPasien()
                        ->find($req->id)
                        ->update(
                            [
                                'status_pemeriksaan'   => 'Boleh Pulang',
                                'updated_by'    => me(),
                                'updated_at' => now(),
                            ]
                        );
                    $this->notify->broadcastingAntrianApotek($req);

                    return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);
                    break;
                case 'sudah_di_bedah':
                    $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->id)->first();
                    if (!$check) {
                        return Response()->json(['status' => 2, 'message' => 'Data corrupt, hubungi developer']);
                    }

                    $rekamMedisPasien = $this->model->rekamMedisPasien()
                        ->find($req->id);

                    $this->model->rekamMedisPasien()
                        ->find($req->id)
                        ->update(
                            [
                                'status_bedah'   => false,
                                'updated_by'    => me(),
                                'updated_at' => now(),
                            ]
                        );



                    $text = '<b>' . Auth::user()->name . '</b> telah memproses rekomendasi tindakan bedah.';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', 0);
                    return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);

                    break;
                case 'pasien_meninggal':
                    $this->model->kamarRawatInapDanBedahDetail()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->update([
                            'status' => 'Done',
                            'tanggal_keluar' => dateStore()
                        ]);


                    $this->model->rekamMedisPasien()
                        ->find($req->id)
                        ->update(
                            [
                                'status_pemeriksaan'   => 'Pasien Meninggal',
                                'updated_by'    => me(),
                                'updated_at' => now(),
                            ]
                        );

                    $rekamMedisPasien = $this->model->rekamMedisPasien()
                        ->find($req->id);

                    $this->model->Pasien()
                        ->where('id', $rekamMedisPasien->pasien_id)
                        ->update([
                            'status' => false,
                        ]);

                    if ($req->pemakaman == 'klinik') {
                        $tindakan = $this->model->tindakan()
                            ->where('name', 'Pemakaman')
                            ->where('binatang_id', $rekamMedisPasien->Pasien->binatang_id)
                            ->where('status', true)
                            ->first();

                        if (!$tindakan) {
                            $idTindakan = $this->model->tindakan()->max('id') + 1;
                            $this->model->tindakan()
                                ->create([
                                    'id'    => $idTindakan,
                                    'name'  => 'Pemakaman',
                                    'binatang_id'   => $rekamMedisPasien->Pasien->binatang_id,
                                    'poli_id'   => 1,
                                    'tarif' => 50000,
                                    'description'   => "Pemakaman Oleh Klinik",
                                    'status'    => true,
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                    'created_at'    => now(),
                                    'updated_at'    => now(),
                                ]);
                        }

                        $idRekamMedisTindakan = $this->model->rekamMedisTindakan()->where('rekam_medis_pasien_id', $req->id)->max('id') + 1;

                        $tindakan = $this->model->tindakan()
                            ->where('name', 'Pemakaman')
                            ->where('binatang_id', $rekamMedisPasien->Pasien->binatang_id)
                            ->where('status', true)
                            ->first();

                        $this->model->rekamMedisTindakan()
                            ->create([
                                'rekam_medis_pasien_id' => $req->id,
                                'id'    => $idRekamMedisTindakan,
                                'tindakan_id'   => $tindakan->id,
                                'tarif' => 50000,
                                'treatment'  => 'Biaya Pemakaman',
                                'created_by'    => me(),
                                'updated_by'    => me(),
                                'created_at'    => now(),
                                'updated_at'    => now(),
                            ]);
                    }
                    return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);
                    break;

                case 'kamar':
                    $rekamMedisPasien = $this->model->rekamMedisPasien()
                        ->find($req->id);
                    $kamarA = $rekamMedisPasien->KamarRawatInapDanBedahDetail->where('status', 'In Use')->first();

                    $this->model->kamarRawatInapDanBedahDetail()
                        ->where('kamar_rawat_inap_dan_bedah_id', $kamarA->kamar_rawat_inap_dan_bedah_id)
                        ->where('id', $kamarA->id)
                        ->update([
                            'status' => 'Move',
                            'status_pindah' => true,
                            'updated_by' => me(),
                        ]);

                    $kamarB = $this->model->kamarRawatInapDanBedah()
                        ->where('id', $req->kamar_rawat_inap_dan_bedah_id)
                        ->first();

                    $idKamarRawatInapDanBedahDetail = $this->model->kamarRawatInapDanBedahDetail()
                        ->where('kamar_rawat_inap_dan_bedah_id', $req->kamar_rawat_inap_dan_bedah_id)
                        ->max('id') + 1;

                    $this->model->kamarRawatInapDanBedahDetail()
                        ->create([
                            'kamar_rawat_inap_dan_bedah_id' => $req->kamar_rawat_inap_dan_bedah_id,
                            'id' => $idKamarRawatInapDanBedahDetail,
                            'pasien_id' => $rekamMedisPasien->pasien_id,
                            'rekam_medis_pasien_id' => $req->id,
                            'tanggal_masuk' => now(),
                            'status' => 'In Use',
                            'created_by' => me(),
                            'updated_by' => me(),
                        ]);


                    $this->notify->broadcastingRawatInap($req->id);
                    $text = 'Pasien <b>' . $rekamMedisPasien->Pasien->name . '</b> dipindahkan dari kamar <b>' . $kamarA->KamarRawatInapDanBedah->name . '</b> ke kamar <b>' . $kamarB->name . '</b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', 0);
                    return Response()->json(['status' => 1, 'message' => 'Berhasil memindahkan pasien']);
                    break;
                default:
                    break;
            }

            return Response()->json(['status' => 1, 'message' => 'Berhasil menambahkan data']);
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
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model->pasien()->with(['ras'])->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);

            switch ($req->param) {
                case 'log':
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    break;
                case 'diagnosa':

                    $this->model->rekamMedisDiagnosa()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_diagnosa')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                case 'treatment':
                    $this->model->rekamMedisTreatment()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_treatment')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                case 'tindakan':
                    $this->model->rekamMedisTindakan()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_tindakan')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                case 'resep':
                    $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();

                    $this->model->rekamMedisResepRacikan()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('rekam_medis_resep_id', $req->id_detail)
                        ->delete();

                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_resep')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                case 'hasil lab':
                    $data =  $this->model->rekamMedisHasilLab()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->first();

                    if (is_file($data->file)) {
                        unlink($data->file);
                    }

                    $this->model->rekamMedisHasilLab()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('id', $req->id_detail)
                        ->delete();
                    $this->model->rekamMedisLogHistory()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('table', 'mp_rekam_medis_hasil_lab')
                        ->where('ref_id', $req->id_detail)
                        ->delete();
                    break;
                    break;
                default:
                    break;
            }

            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function print(Request $req)
    {
        Auth::user()->akses('print', null, true);
        $data = $this->model->pasien()->findOrFail($req->id);
        $pdf = PDF::loadview('quick_menu/rekam_medis/print_rekam_medis', compact('data'))->setPaper('a4', 'potrait');
        return $pdf->stream('PRINT REKAM MEDIS-' . $data->kode . '-' . carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function select2(Request $req)
    {
        switch ($req->param) {
            case 'tindakan_id':
                $rekamMedis = $this->model->rekamMedisPasien()->find($req->id);
                return $this->model->tindakan()
                    ->select('id', DB::raw("name as text"), 'mk_tindakan.*')
                    ->where('binatang_id', $rekamMedis->pasien->binatang_id)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            case 'kamar_rawat_inap_dan_bedah_id':

                $checkKetersediaan = $this->model->kamarRawatInapDanBedah()
                    ->select('id', DB::raw("name as text"), 'mka_kamar_rawat_inap_dan_bedah.*')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->with(['Branch', 'KategoriKamar'])
                    ->get();

                $exclude = [0];

                $kamarTerpakai = $this->model->kamarRawatInapDanBedahDetail()
                    ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                    ->where('status', 'In Use')
                    ->first();

                foreach ($checkKetersediaan as $i => $d) {
                    if ($d->kapasitas <= $d->KamarRawatInapDanBedahDetail->where('status', 'In Use')->count()) {
                        array_push($exclude, $d->id);
                    }
                }

                return $this->model->kamarRawatInapDanBedah()
                    ->select('id', DB::raw("name as text"), 'mka_kamar_rawat_inap_dan_bedah.*')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->with(['Branch', 'KategoriKamar'])
                    ->whereDoesntHave('KamarRawatInapDanBedahDetail', function ($q) use ($kamarTerpakai) {
                        $q->where('id', $kamarTerpakai->id);
                    })
                    ->withCount(['kamarRawatInapDanBedahDetail as terpakai' => function ($q) {
                        $q->where('status', 'In Use');
                    }])
                    ->whereNotIn('id', $exclude)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->q) . '%');
                        $q->orWhereHas('KategoriKamar', function ($q) use ($req) {
                            $q->where(DB::raw("UPPER(name)"), 'like', '%' . strtoupper($req->q) . '%');
                        });
                    })
                    ->paginate(10);
            default:
                # code...
                break;
        }
    }
}

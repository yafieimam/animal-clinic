<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class PemeriksaanPasienController extends Controller
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
        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStfore())
            ->orderBy('created_at', 'ASC')
            ->where('status', 'Waiting')
            ->whereHas('pendaftaranPasien', function ($q) {
                $q->where('status', 'Belum Diperiksa');
            })
            ->where('dokter', me())
            ->first();

        if ($data == null) {
            $message = 'Silahkan menerima pasien';
            return view('quick_menu/penerimaan_pasien/penerimaan_pasien', compact('message'));
        } else {
            $rm = $this->model->rekamMedisPasien()
                ->where('pasien_id', $data->pasien_id)
                ->where('status_pemeriksaan', 'Boleh Pulang')
                ->where('status_pengambilan_obat', true)
                ->where('status_pembayaran', true)
                ->get();

            $pasienActive = $this->model->pendaftaran_pasien()
                ->where('status', 'Belum Diperiksa')
                ->where('pendaftaran_id', $data->id)
                ->first();

            return view('quick_menu/pemeriksaan_pasien/pemeriksaan_pasien', compact('data', 'rm', 'pasienActive'));
        }
    }

    public function getPasien(Request $req)
    {
        $data = $this->model->pendaftaran()
            ->find($req->pendaftaran_id);
        $pasien = $this->model->pasien()
            ->find($req->id);

        $pasien->usia = carbon::parse($pasien->date_of_birth)->diff(Carbon::now())->format('%y tahun %m bulan %d hari');

        $infoPasien = $this->model->pendaftaran_pasien()
            ->where('pendaftaran_id', $req->pendaftaran_id)
            ->where('pasien_id', $req->id)
            ->first();

        $tindakanGrooming = $this->model->tindakan()
            ->where('binatang_id', $pasien->binatang_id)
            ->get();

        $tindakanBedah = $this->model->tindakan()
            ->where('binatang_id', $pasien->binatang_id)
            ->get();

        $pakan = $this->model->itemNonObat()
            ->where('jenis', 'PAKAN')
            ->whereHas('StockFirst', function ($q) use ($data) {
                $q->where('branch_id', $data->branch_id);
            })
            ->with(['StockFirst' => function ($q) use ($data) {
                $q->where('branch_id', $data->branch_id);
            }])
            ->get();

        return view('quick_menu/pemeriksaan_pasien/template_data', compact('data', 'pasien', 'infoPasien', 'tindakanGrooming', 'pakan', 'tindakanBedah'));
    }

    public function getRekamMedis(Request $req)
    {
        $rm = $this->model->rekamMedisPasien()
            ->find($req->id);

        $pasien = $this->model->pasien()
            ->find($rm->pasien_id);

        $infoPasien = $this->model->pendaftaran_pasien()
            ->where('pendaftaran_id', $rm->pendaftaran_id)
            ->where('pasien_id', $rm->pasien_id)
            ->first();

        return view('quick_menu/pemeriksaan_pasien/template_rekam_medis', compact('rm', 'pasien', 'infoPasien'));
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

    public function generateKode($data)
    {
        $tanggal = Carbon::now()->format('Ymd');

        $binatang = $this->model->binatang()->find($data->binatang_id);
        $kode = 'RM-' . $binatang->kode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        // $index = $this->model->rekamMedisPasien()
        //     ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
        //     ->where('kode', 'like', $kode . '%')
        //     ->first();

        // $collect = $this->model->rekamMedisPasien()
        //     ->selectRaw('cast(substring(kode,' . $sub . ') as INTEGER ) as id')
        //     ->get();
        // $count = (int)$index->id;
        // $collect_id = [];
        // for ($i = 0; $i < count($collect); $i++) {
        //     array_push($collect_id, (int)$collect[$i]->id);
        // }

        // $flag = 0;
        // for ($i = 0; $i < $count; $i++) {
        //     if ($flag == 0) {
        //         if (!in_array($i + 1, $collect_id)) {
        //             $index = $i + 1;
        //             $flag = 1;
        //         }
        //     }
        // }

        // if ($flag == 0) {
        //     $index = (int)$index->id + 1;
        // }


        // $index = str_pad($index, 4, '0', STR_PAD_LEFT);

        // $kode = $kode . $index;
        // return $kode;

        // Check if a record with the same kode already exists
        // $existingKodes = $this->model->rekamMedisPasien()
        //     ->where('kode', 'like', $kode . '%')
        //     ->pluck('kode')
        //     ->toArray();

        // // Generate a unique kode
        // $index = 1;
        // while (in_array($kode . str_pad($index, 4, '0', STR_PAD_LEFT), $existingKodes)) {
        //     $index++;
        // }

        // $kode = $kode . str_pad($index, 4, '0', STR_PAD_LEFT);
        // return $kode;

        $countToday = $this->model->rekamMedisPasien()
            ->whereDate('created_at', today())
            ->count();

        $newId = $countToday + 1;

        $kode = $kode . str_pad($newId, 4, '0', STR_PAD_LEFT);
        return $kode;
    }

    public function tambahResep(Request $req)
    {
        $pendaftaran = $this->model->pendaftaran()->find($req->pendaftaran_id);

        $produkObat = $this->model->produkObat()
            ->with([
                'StockFirst' => function ($q) use ($pendaftaran) {
                    $q->where('branch_id', $pendaftaran->branch_id);
                },
            ])
            ->where('status', true)
            ->get();

        return view('quick_menu/pemeriksaan_pasien/template_resep', compact('req', 'produkObat'));
    }

    public function tambahRacikanChild(Request $req)
    {
        $pendaftaran = $this->model->pendaftaran()->find($req->pendaftaran_id);

        $produkObat = $this->model->produkObat()
            ->with([
                'StockFirst' => function ($q) use ($pendaftaran) {
                    $q->where('branch_id', $pendaftaran->branch_id);
                }
            ])
            ->where('status', true)
            ->get();
        return view('quick_menu/pemeriksaan_pasien/template_racikan_child', compact('req', 'produkObat'));
    }

    public function generateKodeJurnal($branchKode)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode =  'JR-' . $branchKode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->jurnal()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->jurnal()
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
            try {
                DB::beginTransaction();
                // DB::statement('LOCK TABLE t_jurnal, mp_rekam_medis_resep, mp_rekam_medis_pasien, mp_rekam_medis_rekomendasi_tindakan_bedah, mp_rekam_medis_pakan, mp_rekam_medis_tindakan, mp_rekam_medis_resep_racikan, mp_rekam_medis_hasil_lab, mp_rekam_medis_catatan, mp_rekam_medis_diagnosa, mka_kamar_rawat_inap_dan_bedah_detail IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                $pendaftaran = $this->model->pendaftaran()->find($req->pendaftaran_id);
                $binatang = $this->model->pasien()->find($req->pasien_id);
                $kode_binatang = $this->model->binatang()->find($binatang->binatang_id);
                $kode = $this->generateKode($binatang);
                // $kode = IdGenerator::generate(['table' => 'mp_rekam_medis_pasien', 'field' => 'kode', 'length' => 20, 'prefix' => "RM-" . $kode_binatang->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);
                $idRekamMedisPasien = $this->model->rekamMedisPasien()->max('id') + 1;
                $needPrint = 0;

                $needPrint += isset($req->tindakan_bedah) ? 1 : 0;
                $needPrint += isset($req->rawat_inap) ? 1 : 0;
                // $needPrint += isset($req->rawat_jalan) ? 1 : 0;
                $needPrint += isset($req->bius) ? 1 : 0;
                // $needPrint += isset($req->grooming) ? 1 : 0;
                $needPrint += isset($req->titip_sehat) ? 1 : 0;
                // Simpan rekam medis

                $check = $this->model->rekamMedisPasien()
                    ->where('pendaftaran_id', $req->pendaftaran_id)
                    ->where('pasien_id', $req->pasien_id)
                    ->first();

                if (!$check) {
                    $this->model->rekamMedisPasien()
                        ->create([
                            'id' => $idRekamMedisPasien,
                            'kode' => $kode,
                            'pasien_id' => $req->pasien_id,
                            'berat' => $req->berat,
                            'suhu' => $req->suhu,
                            'gejala' => $req->anamnesa,
                            'anamnesa' => $req->anamnesa,
                            'diagnosa' => $req->diagnosa,
                            'pendaftaran_id' => $req->pendaftaran_id,
                            'tindakan_bedah' => isset($req->tindakan_bedah) ? true : false,
                            'rawat_inap' => isset($req->rawat_inap) ? true : false,
                            'rawat_jalan' => isset($req->rawat_jalan) ? true : false,
                            'bius' => isset($req->bius) ? true : false,
                            'grooming' => isset($req->grooming) ? true : false,
                            'titip_sehat' => isset($req->titip_sehat) ? true : false,
                            'status' => true,
                            'status_urgent' => $req->status_urgent == 'true' ? true : false,
                            'status_pemeriksaan' => null,
                            'status_pengambilan_obat' => false,
                            // start add 16-Jan-2023
                            'status_apoteker' => 'waiting',
                            //end
                            'status_pembayaran' => false,
                            'tanggal_keluar' => isset($req->rawat_inap) ? null : dateStore(),
                            'catatan' => $req->catatan,
                            'hasil_pemeriksaan'  => $req->hasil_pemeriksaan,
                            'pakan' => $req->pakan,
                            'jenis_grooming' => $req->jenis_grooming,
                            'cukur' => $req->cukur,
                            'created_by' => me(),
                            'updated_by' => me(),
                        ]);

                    $this->model->pasien()
                        ->find($req->pasien_id)
                        ->update([
                            'berat' => $req->berat,
                            'suhu' => $req->suhu,
                            'name'  =>  $req->name,
                            'binatang_id'   =>  $req->binatang_id,
                            'ras_id'    =>  $req->ras_id,
                            'sex'    =>  $req->sex,
                            'life_stage' =>  $req->life_stage,
                            'ciri_khas' =>  $req->ciri_khas,
                            'date_of_birth'  =>  $req->date_of_birth,
                        ]);

                    if (isset($req->tindakan_bedah)) {
                        foreach ($req->rekomendasi_tindakan_bedah as $i => $d) {
                            $idRekamMedisRekomendasiTindakanBedah = $this->model->rekamMedisRekomendasiTindakanBedah()->where('rekam_medis_pasien_id', $idRekamMedisPasien)->max('id') + 1;
                            $rekamMedisRekomendasiTindakanBedah = $this->model->tindakan()
                                ->where('id', $d)
                                ->first();
                            $this->model->rekamMedisRekomendasiTindakanBedah()
                                ->create([
                                    'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                    'id'    => $idRekamMedisRekomendasiTindakanBedah,
                                    'tindakan_id'   => $d,
                                    'tanggal_rekomendasi_bedah' => $req->rekomendasi_tanggal_bedah,
                                    'status_urgensi' => $req->status_urgent == 'true' ? 'true' : 'false',
                                    'status' => 'Released',
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                ]);
                            $text = '<b>' . Auth::user()->name . '</b> menjadwalkan tindakan bedah <b>' . $rekamMedisRekomendasiTindakanBedah->name . '</b> untuk tanggal <b>' . $req->rekomendasi_tanggal_bedah . '</b>';
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_rekomendasi_tindakan_bedah', $idRekamMedisRekomendasiTindakanBedah);
                        }
                    }

                    if (isset($req->rawat_inap) or isset($req->titip_sehat)) {
                        if ($req->pakan != '' or $req->pakan != null) {
                            $stock = decreasingStock('NON OBAT', $req->pakan, $pendaftaran->branch_id, 1, $kode);

                            $produkObat = $this->model->itemNonObat()->find($req->pakan);
                            if (count($stock->getData()->mutasi) == 0) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Stok untuk obat ' . $produkObat->name . ' sudah habis.']);
                            }

                            // $idJurnal = $this->model->jurnal()->max('id') + 1;
                            $kodeJurnal = generateKodeJurnal($pendaftaran->branch->kode)->getData()->kode;
                            // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);
                            $this->model->jurnal()
                                ->create([
                                    // 'id'    => $idJurnal,
                                    'kode'  => $kodeJurnal,
                                    'branch_id' => $pendaftaran->branch_id,
                                    'tanggal'   => dateStore(),
                                    'ref'   => $kode,
                                    'jenis'   => 'PEMERIKSAAN PASIEN',
                                    'dk'    => 'KREDIT',
                                    'description'    => 'PENGELUARAN Stok ' .  $produkObat->name,
                                    'nominal'   => $stock->getData()->total,
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                ]);

                            $idRekamMedisPakan = $this->model->rekamMedisPakan()->where('rekam_medis_pasien_id', $idRekamMedisPasien)->max('id') + 1;

                            $this->model->rekamMedisPakan()
                                ->create([
                                    'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                    'id'    => $idRekamMedisPakan,
                                    'item_non_obat_id'  =>  $req->pakan,
                                    'jumlah'    => 1,
                                    'harga_jual'    => $produkObat->harga,
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                ]);

                            $text = '<b>' . Auth::user()->name . '</b> menambahkan pakan <b>' . $produkObat->name . '</b>';
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_pakan', $idRekamMedisPakan);
                        }
                    }

                    // Simpan Diagnosa
                    // if (isset($req->diagnosa)) {
                    //     foreach ($req->diagnosa as $i => $d) {
                    //         $this->model->rekamMedisDiagnosa()
                    //             ->create([
                    //                 'rekam_medis_pasien_id' => $idRekamMedisPasien,
                    //                 'id'    => $i + 1,
                    //                 'diagnosa'  => $d,
                    //                 'created_by'    => me(),
                    //                 'updated_by'    => me(),
                    //             ]);
                    //     }
                    // }

                    // Simpan Treatment
                    if (isset($req->tindakan_id)) {
                        foreach ($req->treatment as $i => $d) {
                            $this->model->rekamMedisTindakan()
                                ->create([
                                    'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                    'id'    => $i + 1,
                                    'tindakan_id' => $req->tindakan_id[$i],
                                    'tarif' => 0,
                                    'treatment' => $d,
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                ]);
                            $tindakan = $this->model->tindakan()->find($req->tindakan_id[$i]);

                            $text = '<b>' . Auth::user()->name . '</b> melakukan tindakan <b>' . $tindakan->name . ' </b>';
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_tindakan',  $i + 1);
                        }
                    }

                    if (isset($req->grooming)) {
                        $tindakan = $this->model->tindakan()->find($req->jenis_grooming);
                        if ($tindakan) {
                            $idRekamMedisTindakan = $this->model->rekamMedisTindakan()->where('rekam_medis_pasien_id', $idRekamMedisPasien)->max('id') + 1;
                            $this->model->rekamMedisTindakan()
                                ->create([
                                    'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                    'id'    => $idRekamMedisTindakan,
                                    'tindakan_id' => $req->jenis_grooming,
                                    'tarif' => 0,
                                    'treatment' => $tindakan->name,
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                ]);

                            $text = '<b>' . Auth::user()->name . '</b> melakukan tindakan <b>' . $tindakan->name . ' </b>';
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_tindakan',  $idRekamMedisTindakan);
                        }
                    }
                    // dd($req->all());
                    // Simpan Resep
                    if (isset($req->parent_resep)) {
                        foreach ($req->parent_resep as $i => $d) {
                            if ($d == 'racikan') {
                                $this->model->rekamMedisResep()
                                    ->create([
                                        'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                        'id' => $i + 1,
                                        'kategori_obat_id' => $req->jenis_obat_racikan[$i],
                                        'jenis_obat' => $d,
                                        'harga_jual' => 0,
                                        'qty' => $req->qty_racikan[$i],
                                        'description' => $req->description_racikan[$i],
                                        'satuan_obat_id' => $req->satuan_racikan[$i],
                                        'status_resep' =>  isset($req->rawat_inap) ? 'Langsung' : 'Antrian',
                                        'status_pembuatan_obat' => 'Undone',
                                        'created_by' => me(),
                                        'updated_by' => me(),
                                    ]);
                                if ($req->input('racikan_produk_obat_' . $req->index_racikan[$i]) == null) {
                                    DB::rollBack();
                                    return Response()->json(['status' => 2, 'message' => 'Minimal ada 1 obat setiap resep racikan.'], 500);
                                }

                                foreach ($req->input('racikan_produk_obat_' . $req->index_racikan[$i]) as $i1 => $d1) {
                                    $this->model->rekamMedisResepRacikan()
                                        ->create([
                                            'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                            'rekam_medis_resep_id'  => $i + 1,
                                            'id'    => $i1 + 1,
                                            'produk_obat_id'    => $d1,
                                            'qty'   => $req->input('racikan_qty_' . $req->index_racikan[$i])[$i1],
                                            'description'   => $req->description_racikan[$i],
                                            'created_by'    => me(),
                                            'updated_by'    => me(),
                                        ]);
                                }
                                $kategori = $this->model->kategoriObat()->find($req->jenis_obat_racikan[$i]);
                                $resep = $kategori->name . ' ';
                            } elseif ($d == 'non-racikan') {
                                $this->model->rekamMedisResep()
                                    ->create([
                                        'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                        'id' => $i + 1,
                                        'produk_obat_id' => $req->produk_obat_non_racikan[$i],
                                        'status_resep' =>  isset($req->rawat_inap) ? 'Langsung' : 'Antrian',
                                        'status_pembuatan_obat' => 'Undone',
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
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_resep', $i + 1);
                        }
                    } else {
                        $this->model->rekamMedisPasien()
                            ->find($idRekamMedisPasien)
                            ->update([
                                'status_pengambilan_obat' => isset($req->rawat_inap) ? false : true,
                            ]);
                    }

                    // Hasil Lab
                    if (isset($req->hasil_lab)) {
                        foreach ($req->hasil_lab as $i => $d) {
                            $file = $d;
                            $path = 'image/rekam_medis_hasil_lab';
                            $id =  Str::uuid($idRekamMedisPasien . ($i + 1))->toString();
                            $name = $id . '.' . $file->getClientOriginalExtension();
                            $name = $id . '.' . str_replace("application/", "", $name);
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
                                    'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                    'id'    => $i + 1,
                                    'name'  => $file->getClientOriginalName(),
                                    'file'  => $foto,
                                    'created_by'    => me(),
                                    'updated_by'    => me(),
                                ]);
                            $text = '<b>' . Auth::user()->name . '</b> menambahkan hasil lab ' . $file->getClientOriginalName();
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_hasil_lab', $i + 1);
                        }
                    }
                    // Jika Rawat Inap
                    $statusPemeriksaan = 'Boleh Pulang';
                    if (isset($req->rawat_inap)) {
                        $idKamarRawatInapDanBedahDetail = $this->model->kamarRawatInapDanBedahDetail()
                            ->where('kamar_rawat_inap_dan_bedah_id', $req->kamar_rawat_inap_dan_bedah_id)
                            ->max('id') + 1;

                        $this->model->kamarRawatInapDanBedahDetail()
                            ->create([
                                'kamar_rawat_inap_dan_bedah_id' => $req->kamar_rawat_inap_dan_bedah_id,
                                'id' => $idKamarRawatInapDanBedahDetail,
                                'pasien_id' => $req->pasien_id,
                                'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                'tanggal_masuk' => now(),
                                'status' => 'In Use',
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);

                        $statusPemeriksaan = 'Rawat Inap';

                        $kamar = $this->model->kamarRawatInapDanBedah()
                            ->where('id', $req->kamar_rawat_inap_dan_bedah_id)
                            ->first();

                        $pasien =  $this->model->pasien()
                            ->find($req->pasien_id);
                        $text = 'Pasien <b>' . $pasien->name . '</b> dirawat di ruangan <b>' . $kamar->name . '</b>';
                        $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_tindakan', $idKamarRawatInapDanBedahDetail);
                        $this->notify->broadcastingRawatInap($idRekamMedisPasien);
                    }


                    $this->model->rekamMedisDiagnosa()
                        ->create([
                            'rekam_medis_pasien_id' => $idRekamMedisPasien,
                            'id'    => 1,
                            'resource' => 'Pemeriksaan',
                            'diagnosa'  => $req->diagnosa,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    $text = '<b>' . Auth::user()->name . '</b> menambahkan diagnosa ke pasien ini <b>' . $req->diagnosa . ' </b>';
                    $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_diagnosa', 1);

                    $this->model->rekamMedisCatatan()
                        ->create([
                            'rekam_medis_pasien_id' => $idRekamMedisPasien,
                            'id'    => 1,
                            'catatan'  => $req->catatan,
                            'resource' => 'Pemeriksaan',
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    $text = '<b>' . Auth::user()->name . '</b> menambahkan catatan <b>' . $req->catatan . '</b>';
                    $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_diagnosa', 1);

                    $this->model->pendaftaran_pasien()
                        ->where('pendaftaran_id', $req->pendaftaran_id)
                        ->where('pasien_id', $req->pasien_id)
                        ->update([
                            'status' => 'Sudah Diperiksa',
                            'dokter_periksa' => me(),
                        ]);

                    $checkPasien = $this->model->pendaftaran_pasien()
                        ->where('status', 'Belum Diperiksa')
                        ->first();

                    if (!$checkPasien) {
                        $this->model->pendaftaran()
                            ->where('id', $req->pendaftaran_id)
                            ->update(
                                [
                                    'status' => 'Done',
                                ]
                            );
                    }

                    $this->model->rekamMedisPasien()->where('id', $idRekamMedisPasien)
                        ->update([
                            'status_pemeriksaan' => $statusPemeriksaan,
                            'status_bedah' => isset($req->tindakan_bedah) ? true : false,
                        ]);
                } else {
                    DB::rollBack();
                    return Response()->json([
                        'status' => 3,
                        'message' => 'Pasien ini sudah dilakukan pemeriksaan, silahkan memilih pasien yg belum diperiksa.',
                    ]);
                }
                $isNotCompleted = 0;

                $pendaftaran = $this->model->pendaftaran()->find($req->pendaftaran_id);

                $isNotCompleted = $pendaftaran->PendaftaranPasien->where('status', 'Belum Diperiksa')->count();

                if ($statusPemeriksaan == 'Boleh Pulang' and isset($req->parent_resep)) {
                    $pendaftaran = $this->model->pendaftaran()->find($req->pendaftaran_id);
                    $req->request->add(['branch_id' => $pendaftaran->branch_id]);
                    $req->request->add(['id' => $idRekamMedisPasien]);
                    $this->notify->broadcastingAntrianApotek($req);
                } elseif ($statusPemeriksaan == 'Boleh Pulang') {
                    $req->request->add(['branch_id' => $pendaftaran->branch_id]);
                    $req->request->add(['id' => $idRekamMedisPasien]);
                    $this->notify->broadcastingAntrianPembayaran($req);
                }

                if ($isNotCompleted == 0) {
                    $this->model->pendaftaran()->find($req->pendaftaran_id)->update(['status' => 'Completed']);

                    DB::commit();
                    return Response()->json([
                        'status' => 1,
                        'message' => 'Pemeriksaan telah Selesai. mohon segera terima antrean pasien selanjutnya',
                        'not_completed' => $isNotCompleted,
                        'need_print' => $needPrint,
                        'id' => Crypt::encrypt($idRekamMedisPasien)
                    ]);
                } else {
                    DB::commit();
                    return Response()->json([
                        'status' => 2,
                        'message' => 'Pemeriksaan telah Selesai. mohon segera terima antrean pasien selanjutnya',
                        'not_completed' => $isNotCompleted,
                        'need_print' => $needPrint,
                        'id' => Crypt::encrypt($idRekamMedisPasien)
                    ]);
                }
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }
        });
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

    public function print($id)
    {
        Auth::user()->akses('print', null, true);
        $id = crypt::decrypt($id);
        $data = $this->model->rekamMedisPasien()->findOrFail($id);
        $pdf = PDF::loadview('quick_menu/pemeriksaan_pasien/print_pemeriksaan_pasien', compact('data'))->setPaper('a5', 'potrait');
        return $pdf->stream('FORM PERSETUJUAN-' . $data->kode . '-' . carbon::now()->format('Y-m-d') . '.pdf');
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

    public function getListRekamMedis(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->where('pasien_id', $req->id)
            ->orderBy('created_at', 'DESC')
            // ->where('status_pembayaran', true)
            ->get();

        return view('quick_menu/pemeriksaan_pasien/template_list_rekam_medis', compact('data'));
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
            case 'kamar_rawat_inap_dan_bedah_id':

                $checkKetersediaan = $this->model->kamarRawatInapDanBedah()
                    ->select('id', DB::raw("name as text"), 'mka_kamar_rawat_inap_dan_bedah.*')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->with(['Branch', 'KategoriKamar'])
                    ->get();

                $exclude = [0];

                foreach ($checkKetersediaan as $i => $d) {
                    if ($d->kapasitas <= $d->KamarRawatInapDanBedahDetail->where('status', 'In Use')->count()) {
                        array_push($exclude, $d->id);
                    }
                }

                return $this->model->kamarRawatInapDanBedah()
                    ->select('id', DB::raw("name as text"), 'mka_kamar_rawat_inap_dan_bedah.*')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->with(['Branch', 'KategoriKamar'])
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

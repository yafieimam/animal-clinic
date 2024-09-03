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
use App\Events\StoreEventApotek;
use App\Http\Controllers\MonitoringAntrianObatController;
use Yajra\DataTables\Facades\DataTables;

class ApotekController extends Controller
{
    public $model;
    public $notify;
    public $antrian;

    public function __construct(MonitoringAntrianObatController $antrian)
    {
        $this->model   = new Modeler();
        $this->notify  = new NotifyController();
        $this->antrian = $antrian;
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);

        $pasien = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->where('status_pengambilan_obat', false)
            ->where('status_pembayaran', false)
            // ->whereHas('pendaftaran', function ($q) {
            //     $q->where('status', 'Completed');
            // })
            ->where(function ($q) {
                $q->whereHas('rekamMedisResep', function ($q) {
                    $q->where('status_resep', 'Antrian');
                    $q->where('status_pembuatan_obat', 'Undone');
                });

                $q->orWhere('kembali_ke_apotek', 'Ya');
            })
            ->with([
                'rekamMedisResep' => function ($q) {
                    $q->where('status_resep', 'Antrian');
                    $q->where('status_pembuatan_obat', 'Undone');
                    // $q->orderBy('created_at', 'DESC');
                },
                'updatedBy'
            ])
            ->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap'])
            ->orderBy('created_at', 'ASC')
            ->get();

        $pasienRawatInap = $this->model->rekamMedisPasien()
            ->where('status_pemeriksaan', 'Rawat Inap')
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->whereHas('rekamMedisResep', function ($q) {
                $q->where('status_pembuatan_obat', 'Undone');
                $q->where('status_resep', 'Langsung');
            })
            ->where('status_pengambilan_obat', false)
            ->where('status_pembayaran', false)
            // ->whereHas('pendaftaran', function ($q) {
            //     $q->where('status', 'Completed');
            // })
            ->with([
                'KamarRawatInapDanBedahDetail' => function ($q) {
                    $q->where('status', 'In Use');
                },
                'singleRekamMedisResep' => function ($q) {
                    $q->where('status_resep', 'Langsung');
                    $q->where('status_pembuatan_obat', 'Undone');
                    $q->orderBy('created_at', 'DESC');
                },
                'updatedBy'
            ])
            ->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap'])
            ->get();

        // if (Auth::user()->role_id == 1) {
        $pasienRawatInap = $pasienRawatInap->sortBy(function ($data, $key) {
            return $data->singleRekamMedisResep->created_at;
        });
        //     return $pasienRawatInap;
        // }
        // dd($pasienRawatInap);
        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('id', 1)
            // ->where('dokter', me())
            ->first();

        return view('rawat_inap/apotek/apotek', compact('pasien', 'data', 'pasienRawatInap'));
    }

    public function history()
    {
        return view('rawat_inap/apotek/history_apotek');
    }

    public function datatable(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->whereHas('RekamMedisResep', function ($q) use ($req) {
                if ($req->tanggal_awal != '') {
                    $q->where('created_at', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('created_at', '<=', $req->tanggal_akhir);
                }
            })
            ->where('status_pengambilan_obat', true)
            // ->whereHas('pendaftaran', function ($q) {
            //     $q->where('status', 'Completed');
            // })
            ->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap'])
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<button data-id="' . $data->id . '" class="btn btn-primary"  onclick="pilihPasien(this)">Lihat</button>';
            })
            ->addColumn('branch', function ($data) {
                return $data->Pendaftaran->Branch != null ? $data->Pendaftaran->Branch->kode . ' ' . $data->Pendaftaran->Branch->lokasi  : "-";
            })
            ->addColumn('owner', function ($data) {
                return $data->Pasien->Owner != null ? $data->Pasien->Owner->name : "-";
            })
            ->addColumn('pasien', function ($data) {
                return $data->Pasien != null ? $data->Pasien->name : "-";
            })
            ->addColumn('tanggal_buat', function ($data) {
                return count($data->RekamMedisResep) != 0 ? CarbonParse($data->RekamMedisResep[0]->created_at, 'Y-m-d H:i') : "-";
            })
            ->addColumn('status', function ($data) {
                if (!$data->status_pembayaran) {
                    return '<span class="badge badge-warning">Belum Terbayar</span>';
                } else {
                    return '<span class="badge badge-primary">Sudah Terbayar</span>';
                }
            })

            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'status', 'obat'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateOnQueue()
    {
        $pasien = $this->model->rekamMedisPasien()
            ->whereIn('status_pemeriksaan', ['Boleh Pulang', 'Pasien Meninggal', 'Pulang Paksa'])
            ->where(function ($q) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }
            })
            ->where('status_pengambilan_obat', false)
            ->where('status_pembayaran', false)
            // ->whereHas('pendaftaran', function ($q) {
            //     $q->where('status', 'Completed');
            // })
            ->withCount(['KamarRawatInapDanBedahDetail as is_rawat_inap'])
            ->get();
        return Response()->json(['status' => 1, 'message' => 'Berhasil generate data', 'data' => $pasien]);
    }

    public function getApotek(Request $req)
    {
        $data = $this->model->rekamMedisPasien()->with(['updatedBy'])->find($req->id);
        if ($data) {
            if ($data->status_apoteker == 'progress' && Auth::user()->role_id == 7 && $data->updatedBy->role_id == 5) {
                // return Response()->json(['status' => 2, 'message' => 'Maaf, Resep sedang dibuat']);
                return Response()->json(['status' => 2, 'message' => 'Maaf, Dokter sedang membuat resep']);
            }
        }
        $rm = $this->model->rekamMedisPasien()
            ->with([
                'RekamMedisResep' => function ($q) use ($data, $req) {
                    $q->where('status_resep', $req->status_pemeriksaan);
                    $q->with([
                        'ProdukObat' => function ($q) use ($data) {
                            $q->with(
                                [
                                    'StockFirst' => function ($q) use ($data) {
                                        $q->where('branch_id', $data->Pendaftaran->branch_id);
                                    }
                                ]
                            );
                        },
                        'RekamMedisResepRacikan' => function ($q) use ($data) {
                            $q->with(
                                [
                                    'ProdukObat' => function ($q) use ($data) {
                                        $q->with(
                                            [
                                                'StockFirst' => function ($q) use ($data) {
                                                    $q->where('branch_id', $data->Pendaftaran->branch_id);
                                                }
                                            ]
                                        );
                                    },
                                ]
                            );
                        }
                    ]);
                },
            ])
            ->find($req->id);

        $dokter = [];

        foreach ($rm->RekamMedisResep->where('status_pembuatan_obat', 'Undone') as $i => $d) {
            array_push($dokter, $d->created_by);
        }

        $dokter = array_unique($dokter);
        $dokter = array_values($dokter);

        $dokter = $this->model->user()
            ->whereIn('id', $dokter)
            ->get();

        if ($data) {
            $produkObat = $this->model->produkObat()
                ->with([
                    'StockFirst' => function ($q) use ($data) {
                        $q->where('branch_id', $data->Pendaftaran->branch_id);
                    }
                ])
                ->where('status', true)
                ->get();

            return view('rawat_inap/apotek/template_data', compact('rm', 'data', 'produkObat', 'req', 'dokter'));
        }
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
        return view('rawat_inap/apotek/template_resep', compact('req', 'produkObat'));
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
        return view('rawat_inap/apotek/template_racikan_child', compact('req', 'produkObat'));
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
            try {
                DB::beginTransaction();
                // DB::statement('LOCK TABLE t_jurnal, mp_rekam_medis_resep, mp_rekam_medis_resep_racikan IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                $idRekamMedisPasien = $req->id;
                $data = $this->model->rekamMedisPasien()->find($idRekamMedisPasien);

                if ($data->status_apoteker == 'waiting' || $data->status_apoteker == 'revisi') {
                    DB::rollback();
                    return response()->json(['status' => 2, 'message' => 'Data resep harus diproses dahulu']);
                }

                // Simpan Resep
                if ($data->status_pemeriksaan == 'Rawat Inap') {
                    if (isset($req->id_detail)) {
                        $check = $this->model->rekamMedisResep()
                            ->whereNotIn('id', $req->id_detail)
                            ->where('status_pembuatan_obat', 'Undone')
                            ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                            ->where('status_resep', 'Langsung')
                            ->get();

                        foreach ($check as $value) {
                            $obat = $value->ProdukObat ? $value->ProdukObat->name : $value->KategoriObat->name;
                            $text = '<b>' . Auth::user()->name . '</b> menghapus resep <b>' . $obat . ' (Apotek)</b>';
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_resep', $value->id);
                        }
                    } else {
                        $check = $this->model->rekamMedisResep()
                            ->where('status_pembuatan_obat', 'Undone')
                            ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                            ->where('status_resep', 'Langsung')
                            ->get();

                        foreach ($check as $value) {
                            $obat = $value->ProdukObat ? $value->ProdukObat->name : $value->KategoriObat->name;
                            $text = '<b>' . Auth::user()->name . '</b> menghapus resep <b>' . $obat . ' (Apotek)</b>';
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_resep', $value->id);
                        }
                    }

                    $this->model->rekamMedisResepRacikan()
                        ->whereHas('rekamMedisResep', function ($q) {
                            $q->where('status_pembuatan_obat', 'Undone');
                            $q->where('status_resep', 'Langsung');
                        })
                        ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                        ->delete();

                    $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                        ->where('status_pembuatan_obat', 'Undone')
                        ->where('status_resep', 'Langsung')
                        ->delete();
                } else {
                    if (isset($req->id_detail)) {
                        $check = $this->model->rekamMedisResep()
                            ->whereNotIn('id', $req->id_detail)
                            ->where('status_pembuatan_obat', 'Undone')
                            ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                            ->where('status_resep', 'Antrian')
                            ->get();

                        foreach ($check as $value) {
                            $obat = $value->ProdukObat ? $value->ProdukObat->name : $value->KategoriObat->name;
                            $text = '<b>' . Auth::user()->name . '</b> menghapus resep <b>' . $obat . ' </b>';
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_resep', $value->id);
                        }
                    } else {
                        $check = $this->model->rekamMedisResep()
                            ->where('status_pembuatan_obat', 'Undone')
                            ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                            ->where('status_resep', 'Antrian')
                            ->get();

                        foreach ($check as $value) {
                            $obat = $value->ProdukObat ? $value->ProdukObat->name : $value->KategoriObat->name;
                            $text = '<b>' . Auth::user()->name . '</b> menghapus resep <b>' . $obat . ' (Apotek)</b>';
                            $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_resep', $value->id);
                        }
                    }


                    $this->model->rekamMedisResepRacikan()
                        ->whereHas('rekamMedisResep', function ($q) {
                            $q->where('status_pembuatan_obat', 'Undone');
                        })
                        ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                        ->delete();

                    $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                        ->where('status_pembuatan_obat', 'Undone')
                        ->where('status_resep', 'Antrian')
                        ->delete();
                }

                if (isset($req->parent_resep)) {
                    foreach ($req->parent_resep as $i => $d) {
                        if ($d == 'racikan') {
                            $kategoriObat = $this->model->kategoriObat()->find($req->jenis_obat_racikan[$i]);
                            $idRekamMedisResep = $this->model->rekamMedisResep()
                                ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                                ->max('id') + 1;

                            $this->model->rekamMedisResep()
                                ->create([
                                    'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                    'id' => $idRekamMedisResep,
                                    'kategori_obat_id' => $req->jenis_obat_racikan[$i],
                                    'jenis_obat' => $d,
                                    'status_resep' => $req->tab,
                                    'status_pembuatan_obat' => 'Done',
                                    'harga_jual' => 0,
                                    'description' => $req->description_racikan[$i],
                                    'satuan_obat_id' => $req->satuan_racikan[$i],
                                    'qty' => $req->qty_racikan[$i],
                                    'created_by' => $req->created_by[$i],
                                    'created_at' => $req->created_at[$i],
                                    'updated_by' => me(),
                                ]);

                            if ($req->input('racikan_produk_obat_' . $req->index_racikan[$i]) == null) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Minimal ada 1 obat setiap resep racikan.']);
                            }

                            foreach ($req->input('racikan_produk_obat_' . $req->index_racikan[$i]) as $i1 => $d1) {
                                $stock = decreasingStock('OBAT', $d1, $data->Pendaftaran->branch_id, $req->input('racikan_qty_' . $req->index_racikan[$i])[$i1], $data->kode);
                                $produkObat = $this->model->produkObat()->find($req->input('racikan_produk_obat_' . $req->index_racikan[$i])[$i1]);
                                if (count($stock->getData()->mutasi) == 0) {
                                    DB::rollBack();
                                    return Response()->json(['status' => 2, 'message' => 'Stock untuk obat ' . $produkObat->name . ' sudah habis.']);
                                }
                                // $idJurnal = $this->model->jurnal()->max('id') + 1;
                                // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);
                                
                                $this->model->jurnal()
                                    ->create([
                                        // 'id'    => $idJurnal,
                                        'kode'  => generateKodeJurnal($data->Pendaftaran->Branch->kode)->getData()->kode,
                                        // 'kode'  => $kodeJurnal,
                                        'branch_id' => $data->Pendaftaran->branch_id,
                                        'tanggal'   => dateStore(),
                                        'ref'   => $data->kode,
                                        'jenis'   => 'APOTEK',
                                        'dk'    => 'KREDIT',
                                        'description'    => 'PENGELUARAN STOCK ' .  $produkObat->name,
                                        'nominal'   => $stock->getData()->total,
                                        'created_by'    => me(),
                                        'updated_by'    => me(),
                                    ]);


                                $idRekamMedisResepRacikan = $this->model->rekamMedisResepRacikan()
                                    ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                                    ->where('rekam_medis_resep_id', $idRekamMedisResep)
                                    ->max('id') + 1;

                                $this->model->rekamMedisResepRacikan()
                                    ->create([
                                        'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                        'rekam_medis_resep_id'  => $idRekamMedisResep,
                                        'id'    => $idRekamMedisResepRacikan,
                                        'produk_obat_id'    => $d1,
                                        'qty'   => $req->input('racikan_qty_' . $req->index_racikan[$i])[$i1],
                                        'description'   => $req->description_racikan[$i],
                                        'created_by' => $req->created_by[$i],
                                        'created_at' => $req->created_at[$i],
                                        'updated_by'    => me(),
                                    ]);
                            }

                            if ($data->status_pemeriksaan == 'Rawat Inap') {
                                $namaObat = 'Racikan ' . $kategoriObat->name;
                                $this->notify->broadcastingObatSelesai($idRekamMedisPasien, $namaObat);
                            }

                            if ($req->id_detail[$i] == '0') {
                                $obat = $kategoriObat->name;
                                $text = '<b>' . Auth::user()->name . '</b> menambah resep <b>' . $obat . ' (Apotek)</b>';
                                $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_resep', $idRekamMedisResep);
                            }
                        } elseif ($d == 'non-racikan') {
                            $produkObat = $this->model->produkObat()->find($req->produk_obat_non_racikan[$i]);
                            if ($data->status_pemeriksaan == 'Rawat Inap') {
                                $stock = decreasingStock('OBAT', $req->produk_obat_non_racikan[$i], $data->Pendaftaran->branch_id, $req->qty_non_racikan[$i], $data->kode);

                                if (count($stock->getData()->mutasi) == 0) {
                                    DB::rollBack();
                                    return Response()->json(['status' => 2, 'message' => 'Stock untuk obat ' . $produkObat->name . ' sudah habis.']);
                                }

                                // $idJurnal = $this->model->jurnal()->max('id') + 1;
                                // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);

                                // try {
                                $this->model->jurnal()
                                    ->create([
                                        // 'id'    => $idJurnal,
                                        'kode'  => generateKodeJurnal($data->Pendaftaran->Branch->kode)->getData()->kode,
                                        // 'kode'  => $kodeJurnal,
                                        'branch_id' => $data->Pendaftaran->branch_id,
                                        'tanggal'   => dateStore(),
                                        'ref'   => $data->kode,
                                        'jenis'   => 'APOTEK',
                                        'dk'    => 'KREDIT',
                                        'description'    => 'PENGELUARAN STOCK ' .  $produkObat->name,
                                        'nominal'   => $stock->getData()->total,
                                        'created_by' => me(),
                                        'updated_by'    => me(),
                                    ]);
                                // } catch (\Throwable $th) {
                                //     if ($th->getCode() == '23505') {
                                //         DB::rollBack();
                                //         return Response()->json(['status' => 2, 'message' => 'Kode transaksi jurnal sudah terpakai, tekan proses sekali lagi.']);
                                //     }
                                // }
                            }

                            $idRekamMedisResep = $this->model->rekamMedisResep()
                                ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                                ->max('id') + 1;

                            $this->model->rekamMedisResep()
                                ->create([
                                    'rekam_medis_pasien_id' => $idRekamMedisPasien,
                                    'id' => $idRekamMedisResep,
                                    'produk_obat_id' => $req->produk_obat_non_racikan[$i],
                                    'status_resep' => $req->tab,
                                    'status_pembuatan_obat' => 'Done',
                                    'jenis_obat' => $d,
                                    'qty' => $req->qty_non_racikan[$i],
                                    'harga_jual' => convertNumber($req->harga_non_racikan),
                                    'description' => $req->description_non_racikan[$i],
                                    'created_by' => $req->created_by[$i],
                                    'created_at' => $req->created_at[$i],
                                    'updated_by' => me(),
                                ]);
                            if ($data->status_pemeriksaan == 'Rawat Inap') {
                                $namaObat = $produkObat->name;
                                $this->notify->broadcastingObatSelesai($idRekamMedisPasien, $namaObat);
                            }

                            if ($req->id_detail[$i] == '0') {
                                $obat = $produkObat->name;
                                $text = '<b>' . Auth::user()->name . '</b> menambah resep <b>' . $obat . ' (Apotek)</b>';
                                $this->addRekamMedisLogHistory($idRekamMedisPasien, $text, 'mp_rekam_medis_resep', $idRekamMedisResep);
                            }
                        }
                    }
                }

                if ($data->status_pemeriksaan != 'Rawat Inap') {
                    $req->request->add(['branch_id' => $data->Pendaftaran->branch_id]);
                    $req->request->add(['id' => $idRekamMedisPasien]);
                    $this->notify->broadcastingAntrianPembayaran($req);
                }
                $this->model->rekamMedisPasien()
                    ->find($idRekamMedisPasien)
                    ->update([
                        'status_pengambilan_obat' => $data->status_pemeriksaan == 'Rawat Inap' ? false : true,
                        'kembali_ke_apotek' => null,
                    ]);

                // event
                // $request     = new Request(['branch_id' => 4]);
                // $dataAntrian = $this->antrian->getPasien($request);
                // event(new StoreEventApotek($dataAntrian));

                $checkObat = $this->model->rekamMedisResep()
                    ->where('rekam_medis_pasien_id', $idRekamMedisPasien)
                    ->where('status_pembuatan_obat', 'Undone')
                    ->count();

                if ($checkObat == 0) {
                    $this->model->rekamMedisPasien()
                        ->where('id', $req->rekam_medis_pasien_id)
                        ->where('status_pemeriksaan', 'Boleh Pulang')
                        ->update([
                            'status_pengambilan_obat' => true
                        ]);
                }

                DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Update status obat berhasil']);
            } catch (\Throwable $th) {
                DB::rollBack();
                return queryStatus($th->getCode());
            }
        });
    }

    public function generateKodeJurnal($branchKode)
    {
        $tanggal = Carbon::now()->format('Ymd');
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

    public function changeStatusApoteker(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $data = $this->model->rekamMedisPasien()->find($req->id);
            if ($data) {
                if ($data->status_apoteker == 'waiting' || $data->status_apoteker == 'revisi') {
                    $nameUser = $this->model->user()->find($req->user_id);
                    if ($nameUser) {
                        $data->update([
                            'progress_by'     => $nameUser->nama_panggilan != null ? $nameUser->nama_panggilan : 'belum di set',
                            'status_apoteker' => 'progress',
                            'updated_by' => me()
                        ]);

                        DB::commit();

                        // event
                        $request     = new Request(['branch_id' => 4]);
                        $dataAntrian = $this->antrian->getPasien($request);
                        // event(new StoreEventApotek($dataAntrian));

                        return Response()->json(['status' => 1, 'message' => 'Update status antrian berhasil']);
                    }
                    DB::rollback();
                    return Response()->json(['status' => 2, 'message' => 'Maaf, Terjadi Kesalahan']);
                } else if ($data->status_apoteker == 'progress') {
                    DB::rollback();
                    return Response()->json(['status' => 2, 'message' => 'Data sedang diproses']);
                } else {
                    DB::rollback();
                    return Response()->json(['status' => 2, 'message' => 'Data sudah selesai diproses']);
                }
            } else {
                DB::rollback();
                return Response()->json(['status' => 2, 'message' => 'Maaf, Data tidak ditemukan']);
            }
        });
    }

    public function saveResep(Request $req)
    {
        $dataPasien = $this->model->rekamMedisPasien()->find($req->id);
        // if ($dataPasien) {
        //     if ($dataPasien->status_apoteker == 'progress') {
        //         return Response()->json(['status' => 2, 'message' => 'Maaf, Resep sedang dibuat']);
        //     }
        // }

        return DB::transaction(function () use ($req, $dataPasien) {
            $idRekamMedisPasien = $req->id;

            $data = $this->model->rekamMedisResep()->where('rekam_medis_pasien_id', $idRekamMedisPasien)->get();
            if ($data) {
                foreach ($data as $item) {
                    $item->delete();
                }
            }

            $data = $this->model->rekamMedisResepRacikan()->where('rekam_medis_pasien_id', $idRekamMedisPasien)->get();
            if ($data) {
                foreach ($data as $item) {
                    $item->delete();
                }
            }

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
                                'status_resep' =>  $dataPasien->status_pemeriksaan != 'Boleh Pulang' ? 'Langsung' : 'Antrian',
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
                                'status_resep' => $dataPasien->status_pemeriksaan != 'Boleh Pulang' ? 'Langsung' : 'Antrian',
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

                $this->model->rekamMedisPasien()
                    ->find($idRekamMedisPasien)
                    ->update([
                        'status_apoteker' => 'waiting',
                        'progress_by' => null,
                        'updated_by' => me()
                    ]);
            } else {
                $this->model->rekamMedisPasien()
                    ->find($idRekamMedisPasien)
                    ->update([
                        'status_apoteker' => 'waiting',
                        'progress_by' => null,
                        'status_pengambilan_obat' => isset($dataPasien->rawat_inap) ? false : true,
                        'updated_by' => me()
                    ]);
            }

            DB::commit();
            return response()->json(['status' => 1, 'message' => 'Data berhasil diupdate']);
        });
    }

    public function checkLogDeleted(Request $req)
    {
        $data = DB::table('logs')
            ->where('log_type', 'delete')
            ->where('table_name', 'mp_rekam_medis_resep')
            ->where('log_date', '>=', $req->log_date)
            ->get();

        foreach ($data as $key => $value) {
            $value->json = json_decode($value->data);
        }

        return $data;
    }

    public function backToRanap(Request $req)
    {
        // dd($req->all());
        $this->model->rekamMedisPasien()
            ->whereIn('id', $req->rekam_medis_pasien_id)
            ->update([
                'status_pengambilan_obat' => false,
                'status_pemeriksaan' => 'Rawat Inap',
                'tanggal_keluar' => null,
                'status_kepulangan' => null,
            ]);

        $this->model->kamarRawatInapDanBedahDetail()
            ->whereIn('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
            ->where('status_pindah', false)
            ->where('status', 'Done')
            ->update([
                'status' => 'In Use',
                'tanggal_keluar' => null,
            ]);

        $this->model->pasien_meninggal()
            ->whereIn('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
            ->delete();

        foreach ($req->rekam_medis_pasien_id as $key => $value) {
            $text = '<b>' . Auth::user()->name . '</b> mengambalikan ke ranap';
            $this->addRekamMedisLogHistory($value, $text, 'mp_rekam_medis_pasien', $value);
        }

        return Response()->json(['status' => 1, 'message' => 'Berhasil Kembalikan Ke Ranap']);
    }
}

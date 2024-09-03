<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use App\Pasien;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;

class RekamMedisController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        $kamar = $this->model->kategoriKamar()
            ->with([
                'KamarRawatInapDanBedah' => function ($q) {
                    $q->where('branch_id', Auth::user()->branch_id);
                    $q->withCount(['KamarRawatInapDanBedahDetail as jumlah' => function ($q) {
                        $q->select(DB::raw('count(kamar_rawat_inap_dan_bedah_id)'));
                        $q->where('status', 'In Use');
                    }]);
                }
            ])
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
            ->take(10)
            ->get();

        $data = $this->model->pendaftaran()
            // ->where('tanggal', dateStore())
            ->orderBy('created_at', 'ASC')
            ->where('id', 1)
            // ->where('dokter', me())
            ->first();

        $dokter = $this->model->user()
            ->whereHas('role', function ($q) {
                $q->where('type_role', 'DOKTER');
            })
            ->get();

        $hewan = $this->model->binatang()
            ->where('status', true)
            ->get();

        $owner = $this->model->owner()
            ->where('status', true)
            ->get();

        $pakan = $this->model->itemNonObat()
            ->where('jenis', 'PAKAN')
            ->with(['StockFirst' => function ($q) {
                $q->where('branch_id', Auth::user()->branch_id);
            }])
            ->get();

        $itemNonObat = $this->model->itemNonObat()
            ->where('jenis', 'NON PAKAN')
            ->with(['StockFirst' => function ($q) {
                $q->where('branch_id', Auth::user()->branch_id);
            }])
            ->get();

        $statusKepulangan = $this->model->rekamMedisPasien()
            ->distinct()
            ->select('status_kepulangan')
            ->whereNotNull('status_kepulangan')
            ->get();

        return view('management_pasien/rekam_medis/rekam_medis', compact('pasien', 'data', 'kamar', 'dokter', 'hewan', 'owner', 'pakan', 'itemNonObat', 'statusKepulangan'));
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        $formPersetujuanPulangPaksa = '';
        if (Auth::user()->akses('edit')) {
            $edit = '<li>' .
                '<a href="javascript:;" onclick="openModal(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
                '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Edit' .
                '</a>' .
                '</li>';
        }


        $formPersetujuan = '<a href="javascript:;"' .
            'onclick="formPersetujuan(\'' . $data->id . '\',\'' . $data->upload_form_persetujuan . '\')"' .
            'class="dropdown-item text-danger">' .
            '<i class="fa-solid fa-handshake"></i>&nbsp;&nbsp;&nbsp;Form Persetujuan' .
            '</a>';

        if ($data->status_kepulangan == 'Pulang Paksa') {
            $formPersetujuanPulangPaksa = '<a href="javascript:;"' .
                'onclick="formPersetujuanPulangPaksa(\'' . $data->id . '\',\'' . $data->alasan_pulang_paksa . '\',\'' . $data->upload_pulang_paksa . '\',)"' .
                'class="dropdown-item text-danger">' .
                '<i class="fa-solid fa-handshake"></i>&nbsp;&nbsp;&nbsp;Persetujuan Pulang Paksa' .
                '</a>';
        }


        return '<div class="dropdown">' .
            '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
            '<span class="w-5 h-5 flex items-center justify-center">' .
            '<i class="fa fa-bars"></i>' .
            '</span>' .
            '</button>' .
            '<div class="dropdown-menu w-64 ">' .
            '<ul class="dropdown-content">' .
            $edit .
            $formPersetujuan .
            $formPersetujuanPulangPaksa .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function checkIfRmNoPasien(Request $req)
    {
        return $this->model->rekamMedisPasien()
            ->where('pasien_id', $req->pasien_id)
            ->with('Pasien')
            ->get();
    }

    public function datatable(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            // ->where('status_pengambilan_obat', true)
            // ->where('status_pembayaran', true)
            ->has('Pasien')
            ->where(function ($q) use ($req) {

                if ($req->dokter_id != '') {
                    $q->where('created_by', $req->dokter_id);
                }

                if ($req->binatang_id != '') {
                    $q->whereHas('pasien', function ($q) use ($req) {
                        $q->where('binatang_id', $req->binatang_id);
                    });
                }

                if ($req->owner_id != '') {
                    $q->whereHas('pasien', function ($q) use ($req) {
                        $q->where('owner_id', $req->owner_id);
                    });
                }

                if ($req->branch_id != '') {
                    $q->whereHas('pendaftaran', function ($q) use ($req) {
                        $q->where('branch_id', $req->branch_id);
                    });
                }

                if ($req->status_kepulangan != '') {
                    $q->where('status_kepulangan', $req->status_kepulangan);
                }

                if ($req->tanggal_periksa_awal != '') {
                    $q->whereDate('created_at', '>=', $req->tanggal_periksa_awal);
                }

                if ($req->tanggal_periksa_akhir != '') {
                    $q->whereDate('created_at', '<=', $req->tanggal_periksa_akhir);
                }
            })
            ->with([
                'Pasien' => function ($q) {
                    $q->with(['Owner']);
                },
                'CreatedBy'
            ])
            ->orderBy('created_at', 'DESC');

        return DataTables::eloquent($data)
            ->filterColumn('mp_pasien.name', function ($q, $kw) {
                $q->whereHas('Pasien', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->filterColumn('mp_pasien.mp_owner.name', function ($q, $kw) {
                $q->whereHas('pasien.owner', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->filterColumn('users.name', function ($q, $kw) {
                $q->whereHas('CreatedBy', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
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
            ->addColumn('owner', function ($data) {
                return $data->pasien != null ? ($data->pasien != '' ? $data->pasien->owner->name : '-')  : "-";
            })
            ->addColumn('tanggal_masuk', function ($data) {
                return CarbonParse($data->created_at, 'd/m/Y');
            })
            ->addColumn('tanggal_keluar', function ($data) {
                if ($data->tanggal_keluar != null) {
                    return CarbonParse($data->tanggal_keluar, 'd/m/Y');
                }
                if (!$data->rawat_inap) {
                    return CarbonParse($data->updated_at, 'd/m/Y');
                }
            })
            ->addColumn('pasien', function ($data) {
                return $data->pasien != null ? $data->pasien->name  : "-";
            })
            ->addColumn('dokter', function ($data) {
                return $data->CreatedBy != null ? $data->CreatedBy->name  : "-";
            })
            ->addColumn('tindakan_medis', function ($data) {
                $html = '<table style="width:100%">';
                $html .= '<tr><td>Rawat Inap</td><td>' . ($data->rawat_inap ? '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Ya</span>' : '<span class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Tidak</span>') . '</td></tr>';
                $html .= '<tr><td>Status Urgent</td><td>' . ($data->status_urgent ? '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Ya</span>' : '<span class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Tidak</span>') . '</td></tr>';

                return $html;
            })
            ->addColumn('jumlah_data', function ($data) {
                $dataForm = $this->model->rekamMedisPasienUploadFormPersetujuan()->where('rekam_medis_pasien_id', $data->id)->get();
                $html = '<table style="width:100%">';
                $html .= '<tr><td>Catatan</td><td>' . ($data->rekamMedisCatatan->count() + 1) . '</td></tr>';
                $html .= '<tr><td>Obat</td><td>' . ($data->rekamMedisResep->count()) . '</td></tr>';
                $html .= '<tr><td>Tindakan</td><td>' . ($data->rekamMedisTindakan->count()) . '</td></tr>';
                $html .= '<tr><td>Pakan</td><td>' . ($data->rekamMedisPakan->count()) . '</td></tr>';
                $html .= '<tr><td>Bedah</td><td>' . ($data->RekamMedisRekomendasiTindakanBedah->where('status', 'Done')->count()) . '</td></tr>';
                $html .= '<tr><td>Diagnosa</td><td>' . ($data->rekamMedisDiagnosa->count() + 1) . '</td></tr>';
                $html .= '<tr><td>Hasil Lab</td><td>' . ($data->rekamMedisHasilLab->count()) . '</td></tr>';
                $html .= '<tr><td>Form Persetujuan</td><td>' . (count($dataForm) > 0  ? '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Sudah</span>' : '<span class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Belum</span>') . '</td></tr>';

                return $html;
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('image', function ($data) {
                return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->image . '" alt="No image">';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image', 'tindakan_medis', 'jumlah_data'])
            ->addIndexColumn()
            ->make(true);
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
        $rm = $this->model->rekamMedisPasien()
            ->where('id', $req->id)
            // ->where('status_pembayaran', true)
            // ->where('status_pengambilan_obat', true)
            ->first();

        if ($rm) {
            $data = $this->model->Pasien()
                ->find($rm->Pasien->id);

            $infoPasien = $this->model->pendaftaran_pasien()
                ->where('pendaftaran_id', $rm->pendaftaran_id)
                ->where('pasien_id', $rm->Pasien->id)
                ->first();

            return view('management_pasien/rekam_medis/template_data', compact('data', 'rm', 'infoPasien'));
        }
    }

    public function addRekamMedisLogHistory($id, $text, $table, $refId)
    {
        $idRekamMedisLogHistory = $this->model->rekamMedisLogHistory()
            ->where('rekam_medis_pasien_id', $id)
            ->max('id') + 1;

        $this->model->rekamMedisLogHistory()
            ->create([
                'rekam_medis_pasien_id' => $id,
                'id' => $idRekamMedisLogHistory,
                'description' => $text,
                'table' => $table,
                'ref_id' => $refId,
                'created_by' =>  me(),
                'updated_by' => me(),
            ]);
        return true;
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            try {
                DB::beginTransaction();
                // DB::statement('LOCK TABLE t_jurnal, mp_rekam_medis_diagnosa, mp_rekam_medis_catatan, mp_rekam_medis_kondisi_harian, mp_rekam_medis_treatment, mp_rekam_medis_tindakan, mp_rekam_medis_pakan, mp_rekam_medis_non_obat, mp_rekam_medis_resep, mp_rekam_medis_resep_racikan, mp_rekam_medis_hasil_lab, mp_rekam_medis_pasien, mp_pasien_meninggal, mka_kamar_rawat_inap_dan_bedah_detail, mp_rekam_medis_rekomendasi_tindakan_bedah IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                switch ($req->jenis) {
                    case 'diagnosa':
                        $idRekamMedisDiagnosa = $this->model->rekamMedisDiagnosa()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->max('id') + 1;
                        if ($req->id_rekam_medis == null or $req->id_rekam_medis == 'null') {
                            $this->model->rekamMedisDiagnosa()
                                ->create([
                                    'rekam_medis_pasien_id' => $req->id,
                                    'id' => $idRekamMedisDiagnosa,
                                    'diagnosa' => $req->diagnosa,
                                    'resource' => 'Rekam Medis',
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);
                            $text = '<b>' . Auth::user()->name . '</b> menambahkan diagnosa ke pasien ini <b>' . $req->diagnosa . ' </b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_diagnosa', $idRekamMedisDiagnosa);
                        } else {
                            $rekamMedisDiagnosa =  $this->model->rekamMedisDiagnosa()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('id', $req->id_rekam_medis)
                                ->first();

                            $this->model->rekamMedisDiagnosa()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('id', $req->id_rekam_medis)
                                ->update([
                                    'diagnosa' => $req->diagnosa,
                                    'updated_by' => me(),
                                ]);

                            $text = '<b>' . Auth::user()->name . '</b>  merubah diagnosa dari ' . '<b>' . $rekamMedisDiagnosa->diagnosa . '</b>' . ' ke <b>' . $req->diagnosa . ' </b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_diagnosa', $req->id_rekam_medis);
                        }
                        break;
                    case 'catatan':
                        $idRekamMedisCatatan = $this->model->rekamMedisCatatan()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->max('id') + 1;

                        if ($req->id_rekam_medis == null or $req->id_rekam_medis == 'null') {
                            $this->model->rekamMedisCatatan()
                                ->create([
                                    'rekam_medis_pasien_id' => $req->id,
                                    'id' => $idRekamMedisCatatan,
                                    'catatan' => $req->catatan,
                                    'resource' => 'Rekam Medis',
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);
                            $text = '<b>' . Auth::user()->name . '</b> menambahkan catatan <b>' . $req->catatan . '</b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_catatan', $idRekamMedisCatatan);
                        } else {
                            $rekamMedisCatatan =  $this->model->rekamMedisCatatan()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('id', $req->id_rekam_medis)
                                ->first();

                            $this->model->rekamMedisCatatan()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('id', $req->id_rekam_medis)
                                ->update([
                                    'catatan' => $req->catatan,
                                    'updated_by' => me(),
                                ]);

                            $text = '<b>' . Auth::user()->name . '</b>  merubah catatan dari ' . '<b>' . $rekamMedisCatatan->catatan . '</b>' . ' ke <b>' . $req->catatan . ' </b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_catatan', $req->id_rekam_medis);
                        }

                        break;
                    case 'kondisi_harian':
                        $idRekamMedisDiagnosa = $this->model->rekamMedisKondisiHarian()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->max('id') + 1;

                        $this->model->rekamMedisKondisiHarian()
                            ->create([
                                'rekam_medis_pasien_id' => $req->id,
                                'id' => $idRekamMedisDiagnosa,
                                'suhu' => $req->suhu,
                                'makan' => $req->makan,
                                'minum' => $req->minum,
                                'urin' => $req->urin,
                                'feses' => $req->feses,
                                'keterangan' => $req->keterangan,
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);
                        $text = '<b>' . Auth::user()->name . '</b> menambahkan kondisi harian';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_diagnosa', $idRekamMedisDiagnosa);
                        break;
                    case 'treatment':
                        $idRekamMedisTreatment = $this->model->rekamMedisTreatment()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->max('id') + 1;
                        $this->model->rekamMedisTreatment()
                            ->create([
                                'rekam_medis_pasien_id' => $req->id,
                                'id' => $idRekamMedisTreatment,
                                'treatment' => $req->treatment,
                                'tarif' => 0,
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);
                        $text = '<b>' . Auth::user()->name . '</b> memberi treatment <b>' . $req->treatment . ' </b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_treatment', $idRekamMedisTreatment);
                        break;
                    case 'tindakan':
                        $idRekamMedisTindakan = $this->model->rekamMedisTindakan()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->max('id') + 1;

                        $tindakan = $this->model->tindakan()->find($req->tindakan_id);

                        if ($req->id_rekam_medis == null or $req->id_rekam_medis == 'null') {
                            $this->model->rekamMedisTindakan()
                                ->create([
                                    'rekam_medis_pasien_id' => $req->id,
                                    'id' => $idRekamMedisTindakan,
                                    'tindakan_id' => $req->tindakan_id,
                                    'tarif' => $tindakan->tarif,
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);
                            $text = '<b>' . Auth::user()->name . '</b> melakukan tindakan <b>' . $tindakan->name . ' </b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', $idRekamMedisTindakan);
                        } else {
                            $rekamMedisTindakan =  $this->model->rekamMedisTindakan()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('id', $req->id_rekam_medis)
                                ->first();

                            $this->model->rekamMedisTindakan()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('id', $req->id_rekam_medis)
                                ->update([
                                    'tindakan_id' => $req->tindakan_id,
                                    'tarif' => $tindakan->tarif,
                                    'updated_by' => me(),
                                ]);

                            $text = '<b>' . Auth::user()->name . '</b>  merubah tindakan dari ' . '<b>' . $rekamMedisTindakan->Tindakan->name . '</b>' . ' ke <b>' . $tindakan->name . ' </b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', $req->id_rekam_medis);
                        }
                        break;
                    case 'pakan':
                        $rekamMedisPasien = $this->model->rekamMedisPasien()
                            ->find($req->id);

                        $stock = decreasingStock('NON OBAT', $req->pakan, $rekamMedisPasien->pendaftaran->branch_id, 1, $rekamMedisPasien->kode);

                        $produkObat = $this->model->itemNonObat()->find($req->pakan);
                        if (count($stock->getData()->mutasi) == 0) {
                            DB::rollBack();
                            return Response()->json(['status' => 2, 'message' => 'Stok untuk ' . $produkObat->name . ' sudah habis.']);
                        }

                        // $idJurnal = $this->model->jurnal()->max('id') + 1;
                        $kodeJurnal = generateKodeJurnal($rekamMedisPasien->pendaftaran->branch->kode)->getData()->kode;
                        $this->model->jurnal()
                            ->create([
                                // 'id' => $idJurnal,
                                'kode' => $kodeJurnal,
                                'branch_id' => $rekamMedisPasien->pendaftaran->branch_id,
                                'tanggal' => dateStore(),
                                'ref' => $rekamMedisPasien->kode,
                                'jenis' => 'RAWAT INAP',
                                'dk' => 'KREDIT',
                                'description' => 'PENGELUARAN STOK ' . $produkObat->name,
                                'nominal' => $stock->getData()->total,
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);

                        $idRekamMedisPakan = $this->model->rekamMedisPakan()->where('rekam_medis_pasien_id', $req->id)->max('id') + 1;
                        foreach ($stock->getData()->mutasi as $key => $value) {
                            rekamMedisPasienStockMutasi($req->id, $idRekamMedisPakan, 'mp_rekam_medis_pakan', $value->harga, $value->qty, $value->total, $value->id);
                        }

                        $this->model->rekamMedisPakan()
                            ->create([
                                'rekam_medis_pasien_id' => $req->id,
                                'id' => $idRekamMedisPakan,
                                'item_non_obat_id' => $req->pakan,
                                'jumlah' => 1,
                                'harga_jual' => $produkObat->harga,
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);

                        $text = '<b>' . Auth::user()->name . '</b> menambahkan pakan <b>' . $produkObat->name . '</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pakan', $idRekamMedisPakan);
                        break;
                    case 'item_non_obat':
                        $rekamMedisPasien = $this->model->rekamMedisPasien()
                            ->find($req->id);

                        $stock = decreasingStock('NON OBAT', $req->item_non_obat_id, $rekamMedisPasien->pendaftaran->branch_id, 1, $rekamMedisPasien->kode);

                        $produkObat = $this->model->itemNonObat()->find($req->item_non_obat_id);
                        if (count($stock->getData()->mutasi) == 0) {
                            DB::rollBack();
                            return Response()->json(['status' => 2, 'message' => 'Stok untuk ' . $produkObat->name . ' sudah habis.']);
                        }

                        // $idJurnal = $this->model->jurnal()->max('id') + 1;
                        $kodeJurnal = generateKodeJurnal($rekamMedisPasien->pendaftaran->branch->kode)->getData()->kode;
                        $this->model->jurnal()
                            ->create([
                                // 'id' => $idJurnal,
                                'kode' => $kodeJurnal,
                                'branch_id' => $rekamMedisPasien->pendaftaran->branch_id,
                                'tanggal' => dateStore(),
                                'ref' => $rekamMedisPasien->kode,
                                'jenis' => 'RAWAT INAP',
                                'dk' => 'KREDIT',
                                'description' => 'PENGELUARAN STOK ' . $produkObat->name,
                                'nominal' => $stock->getData()->total,
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);

                        $idRekamMedisPakan = $this->model->rekamMedisNonObat()->where('rekam_medis_pasien_id', $req->id)->max('id') + 1;
                        $this->model->rekamMedisNonObat()
                            ->create([
                                'rekam_medis_pasien_id' => $req->id,
                                'id' => $idRekamMedisPakan,
                                'item_non_obat_id' => $req->item_non_obat_id,
                                'jumlah' => 1,
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);

                        $text = '<b>' . Auth::user()->name . '</b> menambahkan <b>' . $produkObat->name . '</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_non_obat', $idRekamMedisPakan);
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
                                        'status_pembuatan_obat' => 'Done',
                                        'status_pembayaran_obat' => 'Done',
                                        'status_resep' => $req->status_resep,
                                        'harga_jual' => 0,
                                        'description' => $req->description_racikan[$i],
                                        'satuan_obat_id' => $req->satuan_racikan[$i],
                                        'qty' => $req->qty_racikan[$i],
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

                                    $rekamMedisPasien = $this->model->rekamMedisPasien()
                                        ->find($req->id);

                                    $check = $this->model->produkObat()
                                        ->find($d1);

                                    $produkObat = $check;

                                    if ($req->input('racikan_qty_' . $req->index_racikan[$i])[$i1] == 0) {
                                        DB::rollBack();
                                        return Response()->json(['status' => 2, 'message' => 'Qty untuk item ' . $produkObat->name . ' tidak boleh Nol']);
                                    }

                                    if (!$produkObat) {
                                        DB::rollBack();
                                        return Response()->json(['status' => 2, 'message' => 'Master Obat untuk ' . $produkObat->name . ' Tidak Ada.']);
                                    }

                                    $stock = decreasingStock('OBAT', $produkObat->id, Auth::user()->Branch->id, $req->input('racikan_qty_' . $req->index_racikan[$i])[$i1], $rekamMedisPasien->kode);
                                    if ($stock->getData()->qty != 0) {
                                        DB::rollBack();
                                        return Response()->json(['status' => 3, 'message' => 'Sisa Stok untuk  '  . $produkObat->name . ' tidak sama dengan yang ada di database. merefresh ulang.']);
                                    }

                                    $this->model->rekamMedisResepRacikan()
                                        ->create([
                                            'rekam_medis_pasien_id' => $req->id,
                                            'rekam_medis_resep_id' => $idRekamMedisResep,
                                            'id' => $i1 + 1,
                                            'produk_obat_id' => $d1,
                                            'qty' => $req->input('racikan_qty_' . $req->index_racikan[$i])[$i1],
                                            'description' => $req->description_racikan[$i],
                                            'created_by' => me(),
                                            'updated_by' => me(),
                                        ]);
                                }
                            } elseif ($d == 'non-racikan') {

                                $rekamMedisPasien = $this->model->rekamMedisPasien()
                                    ->find($req->id);

                                $check = $this->model->produkObat()
                                    ->find($req->produk_obat_non_racikan[$i]);

                                $produkObat = $check;

                                if ($req->qty_non_racikan[$i] == 0) {
                                    DB::rollBack();
                                    return Response()->json(['status' => 2, 'message' => 'Qty untuk item ' . $produkObat->name . ' tidak boleh Nol']);
                                }

                                if (!$produkObat) {
                                    DB::rollBack();
                                    return Response()->json(['status' => 2, 'message' => 'Master Obat untuk ' . $produkObat->name . ' Tidak Ada.']);
                                }

                                $stock = decreasingStock('OBAT', $produkObat->id, Auth::user()->Branch->id, $req->qty_non_racikan[$i], $rekamMedisPasien->kode);

                                if ($stock->getData()->qty != 0) {
                                    DB::rollBack();
                                    return Response()->json(['status' => 3, 'message' => 'Sisa Stok untuk  '  . $produkObat->name . ' tidak sama dengan yang ada di database. merefresh ulang.']);
                                }

                                $this->model->rekamMedisResep()
                                    ->create([
                                        'rekam_medis_pasien_id' => $req->id,
                                        'id' => $idRekamMedisResep,
                                        'produk_obat_id' => $req->produk_obat_non_racikan[$i],
                                        'status_pembuatan_obat' => 'Done',
                                        'status_pembayaran_obat' => 'Done',
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

                                foreach ($stock->getData()->mutasi as $key => $value) {
                                    rekamMedisPasienStockMutasi($req->id, $idRekamMedisResep, 'mp_rekam_medis_resep', $value->harga, $value->qty, $value->total, $value->id);
                                }
                            }

                            $text = '<b>' . Auth::user()->name . '</b> memberikan resep <b>' . $resep . ' </b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_resep', $idRekamMedisResep);
                        }

                        break;
                    case 'hasil lab':
                        foreach ($req->hasil_lab as $i => $d) {
                            $idRekamMedisHasilLab = $this->model->rekamMedisHasilLab()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->max('id') + 1;

                            $file = $d;

                            $path = 'image/rekam_medis_hasil_lab';
                            $id = Str::uuid($req->id . ($i + 1))->toString();
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
                                    'id' => $idRekamMedisHasilLab,
                                    'file' => $foto,
                                    'name' => $file->getClientOriginalName(),
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);

                            $text = '<b>' . Auth::user()->name . '</b> menambahkan hasil lab ' . $file->getClientOriginalName();
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_hasil_lab', $idRekamMedisHasilLab);
                        }
                        break;
                        case 'upload_form_persetujuan':
                            $function = $req->form_persetujuan_function;
    
                            // $file = $req->file('form_persetujuan');
                            // if ($file) {
                            //     $path = 'image/form_persetujuan';
                            //     $id =  Str::uuid($req->form_persetujuan_id)->toString();
                            //     $name = $id . '.' . $file->getClientOriginalExtension();
                            //     $foto = $path . '/' . $name;
                            //     if (is_file($foto)) {
                            //         unlink($foto);
                            //     }
    
                            //     if (!file_exists($path)) {
                            //         $oldmask = umask(0);
                            //         mkdir($path, 0777, true);
                            //         umask($oldmask);
                            //     }
    
                            //     Storage::disk('public_uploads')->put($foto, file_get_contents($file));
                            // }
    
                            // $this->model->rekamMedisPasien()
                            //     ->findOrFail($req->form_persetujuan_id)
                            //     ->update([
                            //         'upload_form_persetujuan' => $foto
                            //     ]);
    
                            if($function == 'New'){
                                $this->model->rekamMedisPasienUploadFormPersetujuan()
                                    ->where('rekam_medis_pasien_id', $req->form_persetujuan_id)
                                    ->delete();
                                
                                foreach ($req->file('form_persetujuan_file') as $index => $file) {
                                    if ($file) {
                                        $path = 'image/form_persetujuan';
                                        $id = Str::uuid($req->form_persetujuan_id)->toString();
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
                                    }
    
                                    $this->model->rekamMedisPasienUploadFormPersetujuan()
                                        ->create([
                                            'rekam_medis_pasien_id' => $req->form_persetujuan_id,
                                            'id' => $req->form_persetujuan_seq[$index],
                                            'file' => $foto
                                        ]);
                                }
                            }else{
                                foreach ($req->file('form_persetujuan_file') as $index => $file) {
                                    $dataPersetujuan = $this->model->rekamMedisPasienUploadFormPersetujuan()
                                        ->where('rekam_medis_pasien_id', $req->form_persetujuan_id)
                                        ->where('id', $req->form_persetujuan_seq[$index])->first();
    
                                    if($dataPersetujuan){
                                        if (is_file($dataPersetujuan->file)) {
                                            unlink($dataPersetujuan->file);
                                        }
    
                                        if ($file) {
                                            $path = 'image/form_persetujuan';
                                            $id = Str::uuid($req->form_persetujuan_id)->toString();
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
                                        }
            
                                        $this->model->rekamMedisPasienUploadFormPersetujuan()
                                            ->where('rekam_medis_pasien_id', $req->form_persetujuan_id)
                                            ->where('id', $req->form_persetujuan_seq[$index])
                                            ->update([
                                                'file' => $foto
                                            ]);
                                    }else{
                                        if ($file) {
                                            $path = 'image/form_persetujuan';
                                            $id = Str::uuid($req->form_persetujuan_id)->toString();
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
                                        }
    
                                        $this->model->rekamMedisPasienUploadFormPersetujuan()
                                            ->create([
                                                'rekam_medis_pasien_id' => $req->form_persetujuan_id,
                                                'id' => $req->form_persetujuan_seq[$index],
                                                'file' => $foto
                                            ]);
                                    }
                                }
                            }
    
                            $text = '<b>' . Auth::user()->name . '</b> mengupload bukti form persetujuan';
                            $this->addRekamMedisLogHistory($req->form_persetujuan_id, $text, 'mp_rekam_medis_pasien', $req->form_persetujuan_id);
                            break;
                        case 'boleh_pulang':
                            $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->id)->first();
                            if (!$check) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Data corrupt, hubungi developer']);
                            }
    
                            $this->model->rekamMedisRekomendasiTindakanBedah()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('status', 'Released')
                                ->delete();
    
                            $rekamMedisPasien = $this->model->kamarRawatInapDanBedahDetail()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->update([
                                    'status' => 'Done',
                                    'tanggal_keluar' => dateStore()
                                ]);
    
                            $checkObat = $this->model->rekamMedisResep()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('status_resep', 'Langsung')
                                ->where('status_pembuatan_obat', 'Undone')
                                ->first();
    
                            if ($checkObat) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Terdapat obat rawat inap yang belum selesai, hubungi apotek.']);
                            }
    
                            $adaObat = $this->model->rekamMedisResep()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('status_pembuatan_obat', '!=', 'Done')
                                ->where('status_resep', 'Antrian')
                                ->get();
    
                            $this->model->rekamMedisPasien()
                                ->find($req->id)
                                ->update(
                                    [
                                        'status_pemeriksaan' => 'Boleh Pulang',
                                        'status_kepulangan' => 'Rekomendasi Dokter',
                                        'tanggal_keluar' => dateStore(),
                                        'updated_by' => me(),
                                        'updated_at' => now(),
                                        'status_pengambilan_obat' => count($adaObat) == 0 ? true : false,
                                    ]
                                );
                            if (count($adaObat) != 0) {
                                $this->notify->broadcastingAntrianApotek($req);
                            }
                            $text = '<b>' . Auth::user()->name . '</b> merubah status  <b>Pasien Boleh Pulang</b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);
                            DB::commit();
                            return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);
                            break;
                        case 'sudah_di_bedah':
                            $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->id)->first();
                            if (!$check) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Data corrupt, hubungi developer']);
                            }
    
                            $rekamMedisPasien = $this->model->rekamMedisPasien()
                                ->find($req->id);
    
                            $this->model->rekamMedisPasien()
                                ->find($req->id)
                                ->update(
                                    [
                                        'status_bedah' => false,
                                        'updated_by' => me(),
                                        'updated_at' => now(),
                                    ]
                                );
    
                            $text = '<b>' . Auth::user()->name . '</b> telah memproses rekomendasi tindakan bedah.';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', 0);
                            DB::commit();
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
                                        'status_pemeriksaan' => 'Pasien Meninggal',
                                        'status_kepulangan' => 'Pasien Meninggal',
                                        'tanggal_keluar' => dateStore(),
                                        'status_pengambilan_obat' =>  true,
                                        'updated_by' => me(),
                                        'updated_at' => now(),
                                    ]
                                );
    
    
    
                            $rekamMedisPasien = $this->model->rekamMedisPasien()
                                ->find($req->id);
    
                            $this->model->rekamMedisResep()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->where('status_pembuatan_obat', 'Undone')
                                ->delete();
    
                            $this->model->Pasien()
                                ->where('id', $rekamMedisPasien->pasien_id)
                                ->update([
                                    'status' => false,
                                ]);
    
                            $this->model->pasien_meninggal()
                                ->create([
                                    'id' => $this->model->pasien_meninggal()->max('id') + 1,
                                    'pasien_id' => $rekamMedisPasien->pasien_id,
                                    'meninggal_saat' => 'Rawat Inap',
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);
    
                            // if ($req->pemakaman == 'klinik') {
                            //     $tindakan = $this->model->tindakan()
                            //         ->where('name', 'Pemakaman')
                            //         ->where('binatang_id', $rekamMedisPasien->Pasien->binatang_id)
                            //         ->where('status', true)
                            //         ->first();
    
                            //     if (!$tindakan) {
                            //         $idTindakan = $this->model->tindakan()->max('id') + 1;
                            //         $this->model->tindakan()
                            //             ->create([
                            //                 'id' => $idTindakan,
                            //                 'name' => 'Pemakaman',
                            //                 'binatang_id' => $rekamMedisPasien->Pasien->binatang_id,
                            //                 'poli_id' => 1,
                            //                 'tarif' => 50000,
                            //                 'description' => "Pemakaman Oleh Klinik",
                            //                 'status' => true,
                            //                 'created_by' => me(),
                            //                 'updated_by' => me(),
                            //                 'created_at' => now(),
                            //                 'updated_at' => now(),
                            //             ]);
                            //     }
    
                            //     $idRekamMedisTindakan = $this->model->rekamMedisTindakan()->where('rekam_medis_pasien_id', $req->id)->max('id') + 1;
    
                            //     $tindakan = $this->model->tindakan()
                            //         ->where('name', 'Pemakaman')
                            //         ->where('binatang_id', $rekamMedisPasien->Pasien->binatang_id)
                            //         ->where('status', true)
                            //         ->first();
    
                            //     $this->model->rekamMedisTindakan()
                            //         ->create([
                            //             'rekam_medis_pasien_id' => $req->id,
                            //             'id' => $idRekamMedisTindakan,
                            //             'tindakan_id' => $tindakan->id,
                            //             'tarif' => 50000,
                            //             'treatment' => 'Biaya Pemakaman',
                            //             'created_by' => me(),
                            //             'updated_by' => me(),
                            //             'created_at' => now(),
                            //             'updated_at' => now(),
                            //         ]);
                            // }
                            $text = '<b>' . Auth::user()->name . '</b> merubah status  <b>Pasien Meninggal</b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);
                            DB::commit();
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
                            $text = 'Pasien <b>' . $rekamMedisPasien->Pasien->name . '</b> dipindahkan dari Ruang Rawat Inap <b>' . $kamarA->KamarRawatInapDanBedah->name . '</b> ke Ruang Rawat Inap <b>' . $kamarB->name . '</b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', 0);
                            DB::commit();
                            return Response()->json(['status' => 1, 'message' => 'Berhasil memindahkan pasien']);
                            break;
                        case 'rekomendasi_tindakan_bedah':
    
                            $tindakan = $this->model->tindakan()->find($req->rekomendasi_tindakan_bedah);
    
                            if ($req->id_rekam_medis == null or $req->id_rekam_medis == 'null') {
                                $idRekamMedisTindakan = $this->model->rekamMedisRekomendasiTindakanBedah()
                                    ->where('rekam_medis_pasien_id', $req->id)
                                    ->max('id') + 1;
    
                                $this->model->rekamMedisRekomendasiTindakanBedah()
                                    ->create([
                                        'rekam_medis_pasien_id' => $req->id,
                                        'id' => $idRekamMedisTindakan,
                                        'tindakan_id' => $req->rekomendasi_tindakan_bedah,
                                        'tanggal_rekomendasi_bedah' => $req->rekomendasi_tanggal_bedah,
                                        'keterangan' => $req->keterangan,
                                        'status' => 'Released',
                                        'created_by' => me(),
                                        'updated_by' => me(),
                                    ]);
    
                                $this->model->rekamMedisPasien()
                                    ->find($req->id)
                                    ->update([
                                        'tindakan_bedah' => true,
                                    ]);
    
                                $tindakanText = $tindakan->name;
                                $tanggalRekomendasiBedah = $req->rekomendasi_tanggal_bedah;
                                $text = '<b>' . Auth::user()->name . "</b> membuat rekomendasi tindakan bedah <b>{$tindakanText}</b> pada tanggal {$tanggalRekomendasiBedah}";
                                $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_rekomendasi_tindakan_bedah', $idRekamMedisTindakan);
                            } else {
                                $idRekamMedisTindakan = $req->id_rekam_medis;
                                $rekamMedisRekomendasiTindakanBedah =  $this->model->rekamMedisRekomendasiTindakanBedah()
                                    ->where('rekam_medis_pasien_id', $req->id)
                                    ->where('id', $req->id_rekam_medis)
                                    ->first();
                                $this->model->rekamMedisRekomendasiTindakanBedah()
                                    ->where('rekam_medis_pasien_id', $req->id)
                                    ->where('id', $idRekamMedisTindakan)
                                    ->update([
                                        'tindakan_id' => $req->rekomendasi_tindakan_bedah,
                                        'tanggal_rekomendasi_bedah' => $req->rekomendasi_tanggal_bedah,
                                        'keterangan' => $req->keterangan,
                                        'status' => 'Released',
                                        'created_by' => me(),
                                        'updated_by' => me(),
                                    ]);
    
    
                                $text = '<b>' . Auth::user()->name . '</b>  merubah tindakan dari ' . '<b>' . $rekamMedisRekomendasiTindakanBedah->Tindakan->name . '</b>' . ' ke <b>' . $tindakan->name . ' </b> ';
                                $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_rekomendasi_tindakan_bedah', $req->id_rekam_medis);
                            }
    
                            break;
                        case 'pulang_paksa':
                            $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->pulang_paksa_id)->first();
    
                            if (!$check) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Data corrupt, hubungi developer']);
                            }
    
                            $this->model->rekamMedisRekomendasiTindakanBedah()
                                ->where('rekam_medis_pasien_id', $req->pulang_paksa_id)
                                ->where('status', 'Released')
                                ->delete();
    
                            $rekamMedisPasien = $this->model->kamarRawatInapDanBedahDetail()
                                ->where('rekam_medis_pasien_id', $req->pulang_paksa_id)
                                ->update([
                                    'status' => 'Done',
                                    'tanggal_keluar' => dateStore()
                                ]);
    
                            $checkObat = $this->model->rekamMedisResep()
                                ->where('status_pembuatan_obat', 'Undone')
                                ->get();
                            
                            $function = $req->pulang_paksa_function;
    
                            if($function == 'New'){
                                $this->model->rekamMedisPasienUploadPulangPaksa()
                                    ->where('rekam_medis_pasien_id', $req->pulang_paksa_id)
                                    ->delete();
                                
                                foreach ($req->file('pulang_paksa_file') as $index => $file) {
                                    if ($file) {
                                        $path = 'image/upload_pulang_paksa';
                                        $id =  Str::uuid($req->pulang_paksa_id)->toString();
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
                                    }
    
                                    $this->model->rekamMedisPasienUploadPulangPaksa()
                                        ->create([
                                            'rekam_medis_pasien_id' => $req->pulang_paksa_id,
                                            'id' => $req->pulang_paksa_seq[$index],
                                            'file' => $foto
                                        ]);
                                }
                            }else{
                                foreach ($req->file('pulang_paksa_file') as $index => $file) {
                                    $dataPulangPaksa = $this->model->rekamMedisPasienUploadPulangPaksa()
                                        ->where('rekam_medis_pasien_id', $req->pulang_paksa_id)
                                        ->where('id', $req->pulang_paksa_seq[$index])->first();
    
                                    if($dataPulangPaksa){
                                        if (is_file($dataPulangPaksa->file)) {
                                            unlink($dataPulangPaksa->file);
                                        }
    
                                        if ($file) {
                                            $path = 'image/upload_pulang_paksa';
                                            $id =  Str::uuid($req->pulang_paksa_id)->toString();
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
                                        }
            
                                        $this->model->rekamMedisPasienUploadPulangPaksa()
                                            ->where('rekam_medis_pasien_id', $req->pulang_paksa_id)
                                            ->where('id', $req->pulang_paksa_seq[$index])
                                            ->update([
                                                'file' => $foto
                                            ]);
                                    }else{
                                        if ($file) {
                                            $path = 'image/upload_pulang_paksa';
                                            $id =  Str::uuid($req->pulang_paksa_id)->toString();
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
                                        }
    
                                        $this->model->rekamMedisPasienUploadPulangPaksa()
                                            ->create([
                                                'rekam_medis_pasien_id' => $req->pulang_paksa_id,
                                                'id' => $req->pulang_paksa_seq[$index],
                                                'file' => $foto
                                            ]);
                                    }
                                }
                            }
    
                            // $fileData = $req->file('pulang_paksa_file');
                            // if ($fileData) {
                            //     foreach ($req->file('pulang_paksa_file') as $index => $file) {
                            //         if ($file) {
                            //             $path = 'image/upload_pulang_paksa';
                            //             $id =  Str::uuid($req->id)->toString();
                            //             $name = $id . '.' . $file->getClientOriginalExtension();
                            //             $foto = $path . '/' . $name;
                            //             if (is_file($foto)) {
                            //                 unlink($foto);
                            //             }
    
                            //             if (!file_exists($path)) {
                            //                 $oldmask = umask(0);
                            //                 mkdir($path, 0777, true);
                            //                 umask($oldmask);
                            //             }
    
                            //             Storage::disk('public_uploads')->put($foto, file_get_contents($file));
                            //         }
    
                            //         $this->model->rekamMedisPasienUploadPulangPaksa()
                            //             ->create([
                            //                 'rekam_medis_pasien_id' => $req->id,
                            //                 'id' => $req->pulang_paksa_seq[$index],
                            //                 'file' => $foto
                            //             ]);
                            //     }
                            // }
    
                            $this->model->rekamMedisPasien()
                                ->find($req->pulang_paksa_id)
                                ->update(
                                    [
                                        'status_pemeriksaan' => 'Boleh Pulang',
                                        'alsa' => 'Boleh Pulang',
                                        'status_kepulangan' => 'Pulang Paksa',
                                        'alasan_pulang_paksa' => $req->alasan_pulang_paksa,
                                        'tanggal_keluar' => dateStore(),
                                        'updated_by' => me(),
                                        'updated_at' => now(),
                                        'status_pengambilan_obat' => count($checkObat) == 0 ? true : false,
                                    ]
                                );
                            $this->notify->broadcastingAntrianApotek($req);
                            $text = '<b>' . Auth::user()->name . '</b> merubah status  <b>Pasien Pulang Paksa</b>';
                            $this->addRekamMedisLogHistory($req->pulang_paksa_id, $text, 'mp_rekam_medis_pasien', $req->pulang_paksa_id);
                            DB::commit();
                            return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);
                            break;
                        default:
                            break;
                    }
    
                    DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Berhasil menambahkan data']);
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Something Wrong. Tell your Admin');
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
            switch ($req->jenis) {
                case 'diagnosa':
                    $rekamMedisDiagnosa =  $this->model->rekamMedisDiagnosa()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->first();
                    $text = '<b>' . Auth::user()->name . '</b>  menghapus diagnosa ' . '<b>' . $rekamMedisDiagnosa->diagnosa . '</b>';

                    $this->addRekamMedisLogHistory($req->rekam_medis_pasien_id, $text, 'mp_rekam_medis_diagnosa', 0);

                    $this->model->rekamMedisDiagnosa()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->delete();

                    break;
                case 'catatan':
                    $rekamMedisCatatan =  $this->model->rekamMedisCatatan()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->first();

                    $text = '<b>' . Auth::user()->name . '</b>  menghapus catatan ' . '<b>' . $rekamMedisCatatan->catatan . '</b>';

                    $this->addRekamMedisLogHistory($req->rekam_medis_pasien_id, $text, 'mp_rekam_medis_catatan', 0);

                    $this->model->rekamMedisCatatan()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->delete();
                    break;
                case 'kondisi_harian':
                    $idRekamMedisDiagnosa = $this->model->rekamMedisKondisiHarian()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->max('id') + 1;

                    $this->model->rekamMedisKondisiHarian()
                        ->create([
                            'rekam_medis_pasien_id' => $req->id,
                            'id' => $idRekamMedisDiagnosa,
                            'suhu' => $req->suhu,
                            'makan' => $req->makan,
                            'minum' => $req->minum,
                            'urin' => $req->urin,
                            'feses' => $req->feses,
                            'keterangan' => $req->keterangan,
                            'created_by' => me(),
                            'updated_by' => me(),
                        ]);
                    $text = '<b>' . Auth::user()->name . '</b> menambahkan kondisi harian';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_diagnosa', $idRekamMedisDiagnosa);
                    break;
                case 'treatment':
                    $idRekamMedisTreatment = $this->model->rekamMedisTreatment()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->max('id') + 1;
                    $this->model->rekamMedisTreatment()
                        ->create([
                            'rekam_medis_pasien_id' => $req->id,
                            'id' => $idRekamMedisTreatment,
                            'treatment' => $req->treatment,
                            'tarif' => 0,
                            'created_by' => me(),
                            'updated_by' => me(),
                        ]);
                    $text = '<b>' . Auth::user()->name . '</b> memberi treatment <b>' . $req->treatment . ' </b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_treatment', $idRekamMedisTreatment);
                    break;
                case 'tindakan':
                    $rekamMedisTindakan =  $this->model->rekamMedisTindakan()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->first();

                    $text = '<b>' . Auth::user()->name . '</b>  menghapus tindakan ' . '<b>' . $rekamMedisTindakan->Tindakan->name . '</b>';

                    $this->addRekamMedisLogHistory($req->rekam_medis_pasien_id, $text, 'mp_rekam_medis_tindakan', 0);

                    $this->model->rekamMedisTindakan()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->delete();
                    break;
                case 'pakan':
                    $rekamMedisPakan =  $this->model->rekamMedisPakan()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->first();

                    $mutasiStock = $this->model->rekamMedisPasienMutasiStock()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('fitur_id', $req->id)
                        ->where('tipe_fitur', 'mp_rekam_medis_pakan')
                        ->get();
                    if (count($mutasiStock) != 0) {
                        foreach ($mutasiStock as $key => $value) {
                            revertStock($value,  $value->tipe_fitur);
                            $this->model->rekamMedisPasienMutasiStock()
                                ->where('rekam_medis_pasien_id', $value->rekam_medis_pasien_id)
                                ->where('id', $value->id)
                                ->delete();
                        }
                    }

                    $this->model->rekamMedisPakan()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->delete();

                    $text = '<b>' . Auth::user()->name . '</b>  menghapus pakan ' . '<b>' . $rekamMedisPakan->ItemNonObat->name . '</b>';

                    $this->addRekamMedisLogHistory($req->rekam_medis_pasien_id, $text, 'mp_rekam_medis_pakan', 0);
                    break;
                case 'item_non_obat':
                    $rekamMedisPasien = $this->model->rekamMedisPasien()
                        ->find($req->id);

                    $stock = decreasingStock('NON OBAT', $req->item_non_obat_id, $rekamMedisPasien->pendaftaran->branch_id, 1, $rekamMedisPasien->kode);

                    $produkObat = $this->model->itemNonObat()->find($req->item_non_obat_id);
                    if (count($stock->getData()->mutasi) == 0) {
                        DB::rollBack();
                        return Response()->json(['status' => 2, 'message' => 'Stok untuk ' . $produkObat->name . ' sudah habis.']);
                    }

                    $idJurnal = $this->model->jurnal()->max('id') + 1;
                    $kodeJurnal = generateKodeJurnal($rekamMedisPasien->pendaftaran->branch->kode)->getData()->kode;
                    $this->model->jurnal()
                        ->create([
                            'id' => $idJurnal,
                            'kode' => $kodeJurnal,
                            'branch_id' => $rekamMedisPasien->pendaftaran->branch_id,
                            'tanggal' => dateStore(),
                            'ref' => $rekamMedisPasien->kode,
                            'jenis' => 'RAWAT INAP',
                            'dk' => 'KREDIT',
                            'description' => 'PENGELUARAN STOK ' .  $produkObat->name,
                            'nominal' => $stock->getData()->total,
                            'created_by' => me(),
                            'updated_by' => me(),
                        ]);

                    $idRekamMedisPakan = $this->model->rekamMedisNonObat()->where('rekam_medis_pasien_id', $req->id)->max('id') + 1;
                    $this->model->rekamMedisNonObat()
                        ->create([
                            'rekam_medis_pasien_id' => $req->id,
                            'id' => $idRekamMedisPakan,
                            'item_non_obat_id' =>  $req->item_non_obat_id,
                            'jumlah' => 1,
                            'created_by' => me(),
                            'updated_by' => me(),
                        ]);

                    $text = '<b>' . Auth::user()->name . '</b> menambahkan <b>' . $produkObat->name . '</b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_non_obat', $idRekamMedisPakan);
                    break;
                case 'resep':
                    $resep =  $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->first();

                    $mutasiStock = $this->model->rekamMedisPasienMutasiStock()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('fitur_id', $req->id)
                        ->where('tipe_fitur', 'mp_rekam_medis_resep')
                        ->get();

                    foreach ($mutasiStock as $key => $value) {
                        revertStock($value,  $value->tipe_fitur);
                        $this->model->rekamMedisPasienMutasiStock()
                            ->where('rekam_medis_pasien_id', $value->rekam_medis_pasien_id)
                            ->where('id', $value->id)
                            ->delete();
                    }



                    $this->model->rekamMedisResepRacikan()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('rekam_medis_resep_id', $req->id)
                        ->delete();

                    $obat = $resep->ProdukObat ? $resep->ProdukObat->name : $resep->KategoriObat->name;

                    $text = '<b>' . Auth::user()->name . '</b>  menghapus resep ' . '<b>' . $obat . '</b>';

                    $this->addRekamMedisLogHistory($req->rekam_medis_pasien_id, $text, 'mp_rekam_medis_resep', 0);
                    $resep =  $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->delete();

                    $checkObat = $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
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
                    break;
                case 'hasil lab':
                    $rekamMedisHasilLab =  $this->model->rekamMedisHasilLab()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->first();

                    if (is_file($rekamMedisHasilLab->file)) {
                        unlink($rekamMedisHasilLab->file);
                    }

                    $text = '<b>' . Auth::user()->name . '</b>  menghapus hasil lab ' . '<b>' . $rekamMedisHasilLab->name . '</b>';

                    $this->addRekamMedisLogHistory($req->rekam_medis_pasien_id, $text, 'mp_rekam_medis_hasil_lab', 0);

                    $this->model->rekamMedisHasilLab()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->delete();

                    break;
                case 'upload_form_persetujuan':

                    $file = $req->file('form_persetujuan');
                    if ($file) {
                        $path = 'image/form_persetujuan';
                        $id = Str::uuid($req->form_persetujuan_id)->toString();
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
                    }

                    $this->model->rekamMedisPasien()
                        ->findOrFail($req->form_persetujuan_id)
                        ->update([
                            'upload_form_persetujuan' => $foto
                        ]);
                    $text = '<b>' . Auth::user()->name . '</b> mengupload bukti form persetujuan';
                    $this->addRekamMedisLogHistory($req->form_persetujuan_id, $text, 'mp_rekam_medis_pasien', $req->form_persetujuan_id);
                    break;
                case 'boleh_pulang':
                    $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->id)->first();
                    if (!$check) {
                        return Response()->json(['status' => 2, 'message' => 'Data corrupt, hubungi developer']);
                    }

                    $this->model->rekamMedisRekomendasiTindakanBedah()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('status', 'Released')
                        ->delete();

                    $rekamMedisPasien = $this->model->kamarRawatInapDanBedahDetail()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->update([
                            'status' => 'Done',
                            'tanggal_keluar' => dateStore()
                        ]);

                    $checkObat = $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('status_resep', 'Langsung')
                        ->where('status_pembuatan_obat', 'Undone')
                        ->first();

                    if ($checkObat) {
                        DB::rollBack();
                        return Response()->json(['status' => 2, 'message' => 'Terdapat obat rawat inap yang belum selesai, hubungi apotek.']);
                    }

                    $adaObat = $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('status_pembuatan_obat', '!=', 'Done')
                        ->where('status_resep', 'Antrian')
                        ->get();

                    $this->model->rekamMedisPasien()
                        ->find($req->id)
                        ->update(
                            [
                                'status_pemeriksaan' => 'Boleh Pulang',
                                'status_kepulangan' => 'Rekomendasi Dokter',
                                'tanggal_keluar' => dateStore(),
                                'updated_by' => me(),
                                'updated_at' => now(),
                                'status_pengambilan_obat' => count($adaObat) == 0 ? true : false,
                            ]
                        );
                    if (count($adaObat) != 0) {
                        $this->notify->broadcastingAntrianApotek($req);
                    }
                    $text = '<b>' . Auth::user()->name . '</b> merubah status  <b>Pasien Boleh Pulang</b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);
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
                                'status_bedah' => false,
                                'updated_by' => me(),
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
                                'status_pemeriksaan' => 'Pasien Meninggal',
                                'status_kepulangan' => 'Pasien Meninggal',
                                'tanggal_keluar' => dateStore(),
                                'status_pengambilan_obat' =>  true,
                                'updated_by' => me(),
                                'updated_at' => now(),
                            ]
                        );



                    $rekamMedisPasien = $this->model->rekamMedisPasien()
                        ->find($req->id);

                    $this->model->rekamMedisResep()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('status_pembuatan_obat', 'Undone')
                        ->delete();

                    $this->model->Pasien()
                        ->where('id', $rekamMedisPasien->pasien_id)
                        ->update([
                            'status' => false,
                        ]);

                    $this->model->pasien_meninggal()
                        ->create([
                            'id' => $this->model->pasien_meninggal()->max('id') + 1,
                            'pasien_id' => $rekamMedisPasien->pasien_id,
                            'meninggal_saat' => 'Rawat Inap',
                            'created_by' => me(),
                            'updated_by' => me(),
                        ]);

                    // if ($req->pemakaman == 'klinik') {
                    //     $tindakan = $this->model->tindakan()
                    //         ->where('name', 'Pemakaman')
                    //         ->where('binatang_id', $rekamMedisPasien->Pasien->binatang_id)
                    //         ->where('status', true)
                    //         ->first();

                    //     if (!$tindakan) {
                    //         $idTindakan = $this->model->tindakan()->max('id') + 1;
                    //         $this->model->tindakan()
                    //             ->create([
                    //                 'id' => $idTindakan,
                    //                 'name' => 'Pemakaman',
                    //                 'binatang_id' => $rekamMedisPasien->Pasien->binatang_id,
                    //                 'poli_id' => 1,
                    //                 'tarif' => 50000,
                    //                 'description' => "Pemakaman Oleh Klinik",
                    //                 'status' => true,
                    //                 'created_by' => me(),
                    //                 'updated_by' => me(),
                    //                 'created_at' => now(),
                    //                 'updated_at' => now(),
                    //             ]);
                    //     }

                    //     $idRekamMedisTindakan = $this->model->rekamMedisTindakan()->where('rekam_medis_pasien_id', $req->id)->max('id') + 1;

                    //     $tindakan = $this->model->tindakan()
                    //         ->where('name', 'Pemakaman')
                    //         ->where('binatang_id', $rekamMedisPasien->Pasien->binatang_id)
                    //         ->where('status', true)
                    //         ->first();

                    //     $this->model->rekamMedisTindakan()
                    //         ->create([
                    //             'rekam_medis_pasien_id' => $req->id,
                    //             'id' => $idRekamMedisTindakan,
                    //             'tindakan_id' => $tindakan->id,
                    //             'tarif' => 50000,
                    //             'treatment' => 'Biaya Pemakaman',
                    //             'created_by' => me(),
                    //             'updated_by' => me(),
                    //             'created_at' => now(),
                    //             'updated_at' => now(),
                    //         ]);
                    // }
                    $text = '<b>' . Auth::user()->name . '</b> merubah status  <b>Pasien Meninggal</b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);
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
                    $text = 'Pasien <b>' . $rekamMedisPasien->Pasien->name . '</b> dipindahkan dari Ruang Rawat Inap <b>' . $kamarA->KamarRawatInapDanBedah->name . '</b> ke Ruang Rawat Inap <b>' . $kamarB->name . '</b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', 0);
                    return Response()->json(['status' => 1, 'message' => 'Berhasil memindahkan pasien']);
                    break;
                case 'rekomendasi_tindakan_bedah':
                    $rekamMedisTindakan =  $this->model->rekamMedisRekomendasiTindakanBedah()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->first();

                    $text = '<b>' . Auth::user()->name . '</b>  menghapus rekomendasi tindakan bedah ' . '<b>' . $rekamMedisTindakan->Tindakan->name . '</b>';
                    $this->addRekamMedisLogHistory($req->rekam_medis_pasien_id, $text, 'mp_rekam_medis_rekomendasi_tindakan_bedah', 0);

                    $this->model->rekamMedisRekomendasiTindakanBedah()
                        ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                        ->where('id', $req->id)
                        ->delete();
                    break;
                case 'pulang_paksa':
                    $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->id)->first();

                    if (!$check) {
                        return Response()->json(['status' => 2, 'message' => 'Data corrupt, hubungi developer']);
                    }

                    $this->model->rekamMedisRekomendasiTindakanBedah()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->where('status', 'Released')
                        ->delete();

                    $rekamMedisPasien = $this->model->kamarRawatInapDanBedahDetail()
                        ->where('rekam_medis_pasien_id', $req->id)
                        ->update([
                            'status' => 'Done',
                            'tanggal_keluar' => dateStore()
                        ]);

                    $checkObat = $this->model->rekamMedisResep()
                        ->where('status_pembuatan_obat', 'Undone')
                        ->get();


                    $file = $req->file('upload_pulang_paksa');
                    if ($file) {
                        $path = 'image/upload_pulang_paksa';
                        $id =  Str::uuid($req->id)->toString();
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
                    } else {
                        $foto = null;
                    }

                    $this->model->rekamMedisPasien()
                        ->find($req->id)
                        ->update(
                            [
                                'status_pemeriksaan' => 'Boleh Pulang',
                                'alsa' => 'Boleh Pulang',
                                'status_kepulangan' => 'Pulang Paksa',
                                'alasan_pulang_paksa' => $req->alasan_pulang_paksa,
                                'upload_pulang_paksa' => $foto,
                                'tanggal_keluar' => dateStore(),
                                'updated_by' => me(),
                                'updated_at' => now(),
                                'status_pengambilan_obat' => count($checkObat) == 0 ? true : false,
                            ]
                        );
                    $this->notify->broadcastingAntrianApotek($req);
                    $text = '<b>' . Auth::user()->name . '</b> merubah status  <b>Pasien Pulang Paksa</b>';
                    $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);
                    return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);
                    break;
                default:
                    break;
            }
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

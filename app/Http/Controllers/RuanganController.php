<?php

namespace App\Http\Controllers;

use App\Models\Modeler;
use App\Models\Pasien;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class RuanganController extends Controller
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
        $kamar = $this->model->kategoriKamar()
            ->with([
                'KamarRawatInapDanBedah' => function ($q) {
                    $q->where('status', 'true');
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
            ->with(['StockFirst' => function ($q) use ($data) {
                $q->where('branch_id', Auth::user()->branch_id);
            }])
            ->get();

        $itemNonObat = $this->model->itemNonObat()
            ->where('jenis', 'NON PAKAN')
            ->with(['StockFirst' => function ($q) use ($data) {
                $q->where('branch_id', Auth::user()->branch_id);
            }])
            ->get();

        return view('rawat_inap/ruangan/ruangan', compact('pasien', 'data', 'kamar', 'dokter', 'hewan', 'owner', 'pakan', 'itemNonObat'));
    }

    public function detail($id)
    {
        return view('quick_menu/pemeriksaan_pasien/pemeriksaan_pasien');
    }

    public function getListRuangan(Request $req)
    {
        $kamar = $this->model->kategoriKamar()
            ->with([
                'KamarRawatInapDanBedah' => function ($q) use ($req) {
                    $q->where('status', 'true');
                    if (!Auth::user()->akses('global')) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    } else {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    }

                    $q->withCount(['KamarRawatInapDanBedahDetail as jumlah' => function ($q) {
                        $q->select(DB::raw('count(kamar_rawat_inap_dan_bedah_id)'));
                        $q->where('status', 'In Use');
                    }]);
                }
            ])
            ->get();

        return view('rawat_inap/ruangan/list_ruangan', compact('kamar'));
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        if (Auth::user()->akses('edit')) {
            $edit = '<li>' .
                '<a href="javascript:;" onclick="openModal(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
                '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Periksa' .
                '</a>' .
                '</li>';
        }



        $lihatRekamMedis = '<a href="javascript:;" onclick="lihatRekamMedis(\'' . $data->pasien_id . '\')" class="dropdown-item text-info">' .
            '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat Rekam Medis' .
            '</a>';

        $formPersetujuan = '<a href="javascript:;"' .
            'onclick="formPersetujuan(\'' . $data->id . '\',\'' . $data->upload_form_persetujuan . '\')"' .
            'class="dropdown-item text-danger">' .
            '<i class="fa-solid fa-handshake"></i>&nbsp;&nbsp;&nbsp;Form Persetujuan' .
            '</a>';


        return '<div class="dropdown">' .
            '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
            '<span class="w-5 h-5 flex items-center justify-center">' .
            '<i class="fa fa-bars"></i>' .
            '</span>' .
            '</button>' .
            '<div class="dropdown-menu w-52 ">' .
            '<ul class="dropdown-content">' .
            $edit .
            $lihatRekamMedis .
            $formPersetujuan .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->where('status_pemeriksaan', 'Rawat Inap')
            ->where(function ($q) use ($req) {
                if ($req->jenis != '') {
                    if ($req->jenis == 'kategori_kamar') {
                        $q->whereHas('KamarRawatInapDanBedahDetailFirst', function ($q) use ($req) {
                            $q->where('status', 'In Use');
                            $q->whereHas('KamarRawatInapDanBedah', function ($q) use ($req) {
                                $q->where('kategori_kamar_id', $req->value);
                            });
                        });
                    } else {
                        $q->whereHas('KamarRawatInapDanBedahDetailFirst', function ($q) use ($req) {
                            $q->where('kamar_rawat_inap_dan_bedah_id', $req->value);
                            $q->where('status', 'In Use');
                        });
                    }
                } else {
                    $q->whereHas('KamarRawatInapDanBedahDetailFirst', function ($q) use ($req) {
                        $q->where('status', 'In Use');
                    });
                }

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
            });

        return DataTables::eloquent($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            ->filterColumn('pasien', function ($q, $kw) {
                $q->whereHas('Pasien', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->filterColumn('owner', function ($q, $kw) {
                $q->whereHas('pasien.owner', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->filterColumn('dokter', function ($q, $kw) {
                $q->whereHas('CreatedBy', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
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
            ->addColumn('pasien', function ($data) {
                return $data->pasien != null ? $data->pasien->name  : "-";
            })
            ->addColumn('dokter', function ($data) {
                return $data->CreatedBy != null ? $data->CreatedBy->name  : "-";
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('image', function ($data) {
                return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->image . '" alt="No image">';
            })
            ->addColumn('form_persetujuan', function ($data) {
                $dataForm = $this->model->rekamMedisPasienUploadFormPersetujuan()->where('rekam_medis_pasien_id', $data->id)->get();
                if (count($dataForm) > 0) {
                    return '<div class="py-1 px-2 rounded-full w-40 text-xs bg-success text-white cursor-pointer font-medium text-center">Sudah Upload</div>';
                } else {
                    return '<div class="py-1 px-2 rounded-full w-40 text-xs bg-danger text-white cursor-pointer font-medium text-center">Belum Upload</div>';
                }
                
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image', 'form_persetujuan'])
            ->addIndexColumn()
            ->make(true);
    }

    public function datatableold(Request $req)
    {
        $aColumns = [
            'kode',
            'pasien',
            'owner',
            'diagnosa',
            'dokter',
        ];

        $limit       = $req->input('length');
        $start       = $req->input('start');
        $dir         = $req->input('order.0.dir') ?? 'desc';
        $data = null;


        $data = $this->model->rekamMedisPasien()
            ->where('status_pemeriksaan', 'Rawat Inap')
            ->where(function ($q) use ($req) {
                if ($req->jenis != '') {
                    if ($req->jenis == 'kategori_kamar') {
                        $q->whereHas('KamarRawatInapDanBedahDetailFirst', function ($q) use ($req) {
                            $q->where('status', 'In Use');
                            $q->whereHas('KamarRawatInapDanBedah', function ($q) use ($req) {
                                $q->where('kategori_kamar_id', $req->value);
                            });
                        });
                    } else {
                        $q->whereHas('KamarRawatInapDanBedahDetailFirst', function ($q) use ($req) {
                            $q->where('kamar_rawat_inap_dan_bedah_id', $req->value);
                            $q->where('status', 'In Use');
                        });
                    }
                } else {
                    $q->whereHas('KamarRawatInapDanBedahDetailFirst', function ($q) use ($req) {
                        $q->where('status', 'In Use');
                    });
                }

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
            });


        $totalData = $data->count();
        $totalFiltered = 0;
        $search = $req->input('search.value');
        if (!empty($req->input('search.value'))) {
            $data = $data->where(function ($query) use ($search) {
                $query
                    ->Orwhere('kode', 'ilike', '%' . $search . '%')
                    ->Orwhere('diagnosa', 'ilike', '%' . $search . '%');
            });

            $data = $data->orWhereHas('pasien', function ($query) use ($search) {
                $query->where('name', 'ilike', "%" . $search . "%");
            });
            $data = $data->orWhereHas('pasien', function ($query) use ($search) {
                $query->WhereHas('owner', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            });
            $data = $data->orWhereHas('CreatedBy', function ($query) use ($search) {
                $query->where('name', 'ilike', "%" . $search . "%");
            });

            $totalFiltered = $data->count();

            $data = $data->offset($start)->limit($limit);

            $requestAll = $req->all();
        } else {
            $totalFiltered = $data->count();
            $data = $data->offset($start)->limit($limit);
            $requestAll = $req->all();
        }
        $data = $data->get();
        $datas = [];
        if (!empty($data)) {
            foreach ($data as $key => $post) {
                $edit = '';
                $delete = '';
                if (Auth::user()->akses('edit')) {
                    $edit = '<li>' .
                        '<a href="javascript:;" onclick="openModal(\'' . $post->id . '\')" class="dropdown-item text-warning">' .
                        '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Periksa' .
                        '</a>' .
                        '</li>';
                }



                $lihatRekamMedis = '<a href="javascript:;" onclick="lihatRekamMedis(\'' . $post->pasien_id . '\')" class="dropdown-item text-info">' .
                    '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat Rekam Medis' .
                    '</a>';

                $formPersetujuan = '<a href="javascript:;"' .
                    'onclick="formPersetujuan(\'' . $post->id . '\',\'' . $post->upload_form_persetujuan . '\')"' .
                    'class="dropdown-item text-danger">' .
                    '<i class="fa-solid fa-handshake"></i>&nbsp;&nbsp;&nbsp;Form Persetujuan' .
                    '</a>';


                $aksi =  '<div class="dropdown">' .
                    '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
                    '<span class="w-5 h-5 flex items-center justify-center">' .
                    '<i class="fa fa-bars"></i>' .
                    '</span>' .
                    '</button>' .
                    '<div class="dropdown-menu w-52 ">' .
                    '<ul class="dropdown-content">' .
                    $edit .
                    $lihatRekamMedis .
                    $formPersetujuan .
                    '</ul>' .
                    '</div>' .
                    '</div>';
                $nestedData['aksi'] = $aksi;
                if ($post->status == true) {
                    $nestedData['status'] = '<button class="btn btn-info btn-round btn-xs" onclick="gantiStatus(false,\'' . $post->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    $nestedData['status'] = '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(true,\'' . $post->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
                $nestedData['icon'] = '<i class="' . $post->icon . ' text-2xl"></i>';
                $nestedData['owner'] = $post->pasien != null ? ($post->pasien != '' ? $post->pasien->owner->name : '-')  : "-";
                $nestedData['pasien'] = $post->pasien != null ? $post->pasien->name  : "-";
                $nestedData['dokter'] = $post->CreatedBy != null ? $post->CreatedBy->name  : "-";
                $nestedData['sequence'] = '<input type="number" value="' . $post->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $post->id . '\',this)">';
                $nestedData['image'] = '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $post->image . '" alt="No image">';
                $nestedData['DT_RowIndex'] = $key + 1 + $start;
                $nestedData['kode'] = $post->kode;
                $nestedData['diagnosa'] = $post->diagnosa;
                $datas[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($req->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            'start' => $start,
            'limit' => $limit,
            "order" => false,
            'dir' => $dir,
            'search' => $search,
            "data" => $datas,
        );


        return  $json_data;

        // wait saya code di vscode saya
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

        $len = strlen($index);

        if ($len < 5) {
            $pad = 4;
        } else {
            $pad = $len;
        }

        $index = str_pad($index, $pad, '0', STR_PAD_LEFT);

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

        return view('rawat_inap/ruangan/template_resep', compact('req', 'produkObat'));
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
        return view('rawat_inap/ruangan/template_racikan_child', compact('req', 'produkObat'));
    }

    public function getRekamMedis(Request $req)
    {
        $rm = $this->model->rekamMedisPasien()
            ->where('id', $req->id)
            ->where('status_pembayaran', false)
            ->where('status_pemeriksaan', 'Rawat Inap')
            ->first();

        // if (Auth::user()->role_id == 1) {
        //     dd($rm);
        // }
        if ($rm) {
            $data = $this->model->Pasien()
                ->find($rm->Pasien->id);

            $infoPasien = $this->model->pendaftaran_pasien()
                ->where('pendaftaran_id', $rm->pendaftaran_id)
                ->where('pasien_id', $rm->Pasien->id)
                ->first();
            return view('rawat_inap/ruangan/template_data', compact('data', 'rm', 'infoPasien'));
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

    public function generateKodeJurnal($branchKode)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'JR-' . $branchKode . '-' . $tanggal . '-';
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
                                    'resource' => 'Rawat Inap',
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
                                    'resource' => 'Rawat Inap',
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
                                'item_non_obat_id' => $req->item_non_obat_id,
                                'jumlah' => 1,
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);

                        $text = '<b>' . Auth::user()->name . '</b> menambahkan <b>' . $produkObat->name . '</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_non_obat', $idRekamMedisPakan);
                        break;
                    case 'resep':
                        $check = $this->model->rekamMedisPasien()
                            ->where('id', $req->id)
                            ->first();

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
                            $text = '<b>' . Auth::user()->name . '</b> memberikan resep <b>' . $resep . ' </b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_resep', $idRekamMedisResep);
                        }

                        $req->request->add([
                            'id_rekam_medis_resep' => $idRekamMedisResep
                        ]);

                        if ($check) {
                            $check->update([
                                'status_apoteker' => 'waiting',
                                'progress_by' => null
                            ]);
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

                        if($function == 'New'){
                            $this->model->rekamMedisPasienUploadFormPersetujuan()
                                ->where('rekam_medis_pasien_id', $req->form_persetujuan_id)
                                ->delete();
                            
                            foreach ($req->file('form_persetujuan_file') as $index => $file) {
                                if ($file) {
                                    $path = 'image/form_persetujuan';
                                    $id =  Str::uuid($req->form_persetujuan_id)->toString();
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
                                        $id =  Str::uuid($req->form_persetujuan_id)->toString();
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
                                        $id =  Str::uuid($req->form_persetujuan_id)->toString();
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
                            ->update([
                                'status' => 'Cancel',
                                'updated_by' => me(),
                                'updated_at' => now(),
                            ]);

                        $rekamMedisPasien = $this->model->kamarRawatInapDanBedahDetail()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->where('status_pindah', false)
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

                        $text = '<b>' . Auth::user()->name . '</b> merubah status  <b>Pasien Boleh Pulang</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);
                        DB::commit();
                        if (count($adaObat) != 0) {
                            $this->notify->broadcastingAntrianApotek($req);
                        }
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

                        $this->model->rekamMedisRekomendasiTindakanBedah()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->where('status', 'Released')
                            ->update([
                                'status' => 'Cancel',
                                'updated_by' => me(),
                                'updated_at' => now(),
                            ]);

                        $this->model->kamarRawatInapDanBedahDetail()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->where('status_pindah', false)
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
                                'rekam_medis_pasien_id' => $req->id,
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
                                'tanggal_keluar' => dateStore(),
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

                        foreach ($req->rekomendasi_tindakan_bedah as $key => $value) {
                            $idRekamMedisTindakan = $this->model->rekamMedisRekomendasiTindakanBedah()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->max('id') + 1;

                            $tindakan = $this->model->tindakan()->find($value);
                            if (!$tindakan) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Tidak ada tindakan yang dipilih.']);
                            }

                            $this->model->rekamMedisRekomendasiTindakanBedah()
                                ->create([
                                    'rekam_medis_pasien_id' => $req->id,
                                    'id' => $idRekamMedisTindakan,
                                    'tindakan_id' => $value,
                                    'tanggal_rekomendasi_bedah' => $req->rekomendasi_tanggal_bedah,
                                    'keterangan' => $req->keterangan,
                                    'status_urgensi' => $req->status_urgent == 'true' ? 'true' : 'false',
                                    'status' => 'Released',
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);

                            $text = '<b>' . Auth::user()->name . '</b> menjadwalkan tindakan bedah <b>' . $tindakan->name . '</b> untuk tanggal <b>' . $req->rekomendasi_tanggal_bedah . '</b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_rekomendasi_tindakan_bedah', $idRekamMedisTindakan);
                        }

                        break;
                    case 'pulang_paksa':
                        $checkObat = $this->model->rekamMedisResep()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->where('status_resep', 'Langsung')
                            ->where('status_pembuatan_obat', 'Undone')
                            ->first();

                        if ($checkObat) {
                            DB::rollBack();
                            return Response()->json(['status' => 2, 'message' => 'Terdapat obat rawat inap yang belum selesai, hubungi apotek.']);
                        }

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
                            ->where('status_pindah', false)
                            ->update([
                                'status' => 'Done',
                                'tanggal_keluar' => dateStore()
                            ]);

                        $fileData = $req->file('pulang_paksa_file');
                        if ($fileData) {
                            foreach ($req->file('pulang_paksa_file') as $index => $file) {
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
                                }

                                $this->model->rekamMedisPasienUploadPulangPaksa()
                                    ->create([
                                        'rekam_medis_pasien_id' => $req->id,
                                        'id' => $req->pulang_paksa_seq[$index],
                                        'file' => $foto
                                    ]);
                            }
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
                                    'alsa' => 'Boleh Pulang',
                                    'status_kepulangan' => 'Pulang Paksa',
                                    'alasan_pulang_paksa' => $req->alasan_pulang_paksa,
                                    'tanggal_keluar' => dateStore(),
                                    'updated_by' => me(),
                                    'updated_at' => now(),
                                    'status_pengambilan_obat' => count($adaObat) == 0 ? true : false,
                                ]
                            );
                        $text = '<b>' . Auth::user()->name . '</b> merubah status  <b>Pasien Pulang Paksa</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);

                        DB::commit();
                        $this->notify->broadcastingAntrianApotek($req);
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
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model->rekamMedisPasien()->where('id', $req->id)->first();
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
                        $q->where('kamar_rawat_inap_dan_bedah_id', $kamarTerpakai->kamar_rawat_inap_dan_bedah_id);
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

    public function print(Request $req)
    {
        $data = $this->model->rekamMedisPasien()->findOrFail($req->id);
        $pdf = PDF::loadview('quick_menu/pemeriksaan_pasien/print_pemeriksaan_pasien', compact('data'))->setPaper('a4', 'potrait');
        return $pdf->stream('FORM PERSETUJUAN-' . $data->kode . '-' . carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function printPulangPaksa(Request $req)
    {
        if (!isset($req->rekam_medis)) {
            $data = $this->model->rekamMedisPasien()->findOrFail($req->id);
            $pdf = PDF::loadview('quick_menu/pemeriksaan_pasien/print_pulang_paksa_pemeriksaan_pasien', compact('data'))->setPaper('a4', 'potrait');
            return $pdf->stream('FORM PERSETUJUAN-' . $data->kode . '-' . carbon::now()->format('Y-m-d') . '.pdf');
        } else {
            $data = $this->model->rekamMedisPasien()->findOrFail($req->id);
            return redirect($data->upload_pulang_paksa);
        }
    }

    public function editFormPersetujuan(Request $req)
    {
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model->rekamMedisPasienUploadFormPersetujuan()->where('rekam_medis_pasien_id', $req->id)->get();

        // if(!empty($data)){
        //     foreach ($data as $item) {
        //         $item->file_name = basename($item->file);
        //         $item->file_size = filesize($item->file);
        //     }
        // }

        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function deleteFormPersetujuan(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);

            $data =  $this->model->rekamMedisPasienUploadFormPersetujuan()
                ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                ->where('id', $req->id)
                ->first();

            if (is_file($data->file)) {
                unlink($data->file);
            }

            $this->model->rekamMedisPasienUploadFormPersetujuan()
                ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                ->where('id', $req->id)
                ->delete();

            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function editPulangPaksa(Request $req)
    {
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }
        $data = $this->model->rekamMedisPasienUploadPulangPaksa()->where('rekam_medis_pasien_id', $req->id)->get();

        // if(!empty($data)){
        //     foreach ($data as $item) {
        //         $item->file_name = basename($item->file);
        //         $item->file_size = filesize($item->file);
        //     }
        // }

        return Response()->json(['status' => 1, 'data' => $data]);
    }
}

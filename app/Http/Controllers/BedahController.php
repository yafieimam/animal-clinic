<?php

namespace App\Http\Controllers;

use App\Exports\BedahExport;
use App\Models\Modeler;
use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class BedahController extends Controller
{
    public $model;
    public $notify;
    public function __construct()
    {
        $this->model = new Modeler();
        $this->notify = new NotifyController();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        $kamar = $this->model->KamarRawatInapDanBedah()
            ->where('status', true)
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

        // $rekomendasiTindakanBedah = $this->model->tindakan()
        //     ->whereHas('poli', function ($q) {
        //         $q->where('name', 'Bedah');
        //     })
        //     ->where('status', true)
        //     ->get();

        $rekomendasiTindakanBedah = $this->model->tindakan()
            ->has('RekamMedisRekomendasiTindakanBedah')
            ->where('status', true)
            ->take(10)
            ->get();

        return view('rawat_inap/bedah/bedah', compact('pasien', 'data', 'kamar', 'dokter', 'hewan', 'owner', 'pakan', 'itemNonObat', 'rekomendasiTindakanBedah'));
    }

    public function detail($id)
    {
        return view('quick_menu/pemeriksaan_pasien/pemeriksaan_pasien');
    }

    public function aksi($data)
    {
        $edit = '';
        $editCatatan = '';
        $delete = '';

        if (Auth::user()->akses('edit')) {
            $edit = '<li>' .
            '<a href="javascript:;" onclick="openModal(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
                '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Proses Bedah' .
                '</a>' .
                '</li>';

            $editCatatan = '<li>' .
            '<a href="javascript:;" onclick="openModalCatatan(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
                '<i class="far fa-comment"></i>&nbsp;&nbsp;&nbsp;Edit Catatan' .
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
            $editCatatan .
            $lihatRekamMedis .
            $formPersetujuan .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model->RekamMedisPasien()::query()
            ->whereHas('rekamMedisRekomendasiTindakanBedah', function ($q) use ($req) {
                if ($req->rekomendasi_tanggal_bedah != '') {
                    $q->where('tanggal_rekomendasi_bedah', $req->rekomendasi_tanggal_bedah);
                }

                if ($req->tindakan_id_filter != '') {
                    $q->where('tindakan_id', $req->tindakan_id_filter);
                }

                if ($req->status != '') {
                    $q->where('status', $req->status);
                } else {
                    $q->where('status', 'Released');
                }
            })
            ->where(function ($q) use ($req) {
                if ($req->ruangan_rawat_inap != '') {
                    $q->whereHas('KamarRawatInapDanBedahDetail', function ($q) use ($req) {
                        $q->where('kamar_rawat_inap_dan_bedah_id', $req->ruangan_rawat_inap);
                    });
                }

                if (!Auth::user()->akses('global')) {
                    $q->whereHas('Pendaftaran', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                } else {
                    if ($req->branch_id != '') {
                        $q->whereHas('pendaftaran', function ($q) use ($req) {
                            $q->where('branch_id', $req->branch_id);
                        });
                    }
                }
            });

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                if ($data->rekamMedisRekomendasiTindakanBedah->where('status', 'Released')->count() != 0) {
                    return $this->aksi($data);
                } else {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium text-center">Done</div>';
                }
            })
            ->addColumn('icon', function ($data) {
                return '<i class="' . $data->icon . ' text-2xl"></i>';
            })
            ->filterColumn('mp_pasien.name', function ($q, $kw) {
                $q->whereHas('Pasien', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->filterColumn('pasien', function ($q, $kw) {
                $q->whereHas('Pasien', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->filterColumn('owner', function ($q, $kw) {
                $q->whereHas('Pasien.Owner', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->filterColumn('ruangan', function ($q, $kw) {
                $q->whereHas('KamarRawatInapDanBedahDetail.KamarRawatInapDanBedah', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
                $q->whereHas('KamarRawatInapDanBedahDetail', function ($query) use ($kw) {
                    $query->where('status_pindah', false);
                });
            })
            ->filterColumn('diagnosa', function ($q, $kw) {
                return $q->where(DB::raw('LOWER(diagnosa)'), 'LIKE', '%' . strtolower($kw) . '%');
            })
            ->filterColumn('catatan', function ($q, $kw) {
                return $q->where(DB::raw('LOWER(catatan)'), 'LIKE', '%' . strtolower($kw) . '%');
            })
            ->filterColumn('kode', function ($q, $kw) {
                return $q->where(DB::raw('LOWER(kode)'), 'LIKE', '%' . strtolower($kw) . '%');
            })
            ->filterColumn('informasi_bedah', function ($q, $kw) {
                $q->whereHas('RekamMedisRekomendasiTindakanBedah.tindakan', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
                $q->orWhereHas('RekamMedisRekomendasiTindakanBedah.UpdatedBy', function ($query) use ($kw) {
                    return $query->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($kw) . '%');
                });
            })
            ->addColumn('kode', function ($data) {
                return $data->kode;
            })
            ->addColumn('owner', function ($data) {
                return $data->pasien != null ? ($data->pasien != '' ? $data->pasien->owner->name : '-') : "-";
            })
            ->addColumn('pasien', function ($data) {
                return $data->pasien != null ? $data->pasien->name : "-";
            })
            ->addColumn('dokter_poli', function ($data) {
                return $data->CreatedBy != null ? $data->CreatedBy->name : "-";
            })
            ->addColumn('informasi_bedah', function ($data) {
                return view('rawat_inap.bedah.template_informasi_bedah', compact('data'));
            })
            ->addColumn('diagnosa', function ($data) {
                return $data->diagnosa;
            })
            ->addColumn('catatan', function ($data) {
                return $data->catatan;
            })
            ->addColumn('ruangan', function ($data) {
                $html = '';
                foreach ($data->KamarRawatInapDanBedahDetail->sortBy('created_at') as $key => $value) {
                    $html = $value->KamarRawatInapDanBedah->name;
                }

                return $html;
            })
            ->addColumn('sequence', function ($data) {
                return '<input type="number" value="' . $data->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $data->id . '\',this)">';
            })
            ->addColumn('image', function ($data) {
                return '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->Pasien->image . '" alt="No image">';
            })
            ->addColumn('status_urgensi', function ($data) {
                if ($data->status_urgensi == 'true') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Urgent</div>';
                } else {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium"  style="background:blue">Normal</div>';
                }
            })
            ->addColumn('status', function ($data) {
                if ($data->rekamMedisRekomendasiTindakanBedah->where('status', 'Released')->count() != 0) {
                    return '<div class="py-1 px-2 rounded-full text-cener w-20 text-xs bg-warning text-white cursor-pointer font-medium">Menunggu</div>';
                } else {
                    return "<div class='py-1 px-2 rounded-full text-xs bg-info w-24 text-center text-white cursor-pointer font-medium'>Done By {$data->rekamMedisRekomendasiTindakanBedah->sortByDesc('updated_at')[0]->UpdatedBy->nama_panggilan}</div>";
                }
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'image', 'kode', 'bedah', 'status_urgensi', 'catatan', 'informasi_bedah'])
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
            'catatan',
            'bedah',
            'ruangan',
            'tanggal_rekomendasi_bedah',
            'status_urgensi',
            'status',
        ];
        $limit = $req->input('length');
        $start = $req->input('start');
        $dir = $req->input('order.0.dir') ?? 'desc';
        $data = null;

        $data = $this->model->rekamMedisRekomendasiTindakanBedah()
            ->whereHas('RekamMedisPasien', function ($q) {
                $q->whereHas('Pendaftaran', function ($q) {
                    $q->where('branch_id', Auth::user()->branch_id);
                });
            })
            ->where('status', 'Released')
            ->where(function ($q) use ($req) {
                if ($req->tindakan_id_filter != '') {
                    $q->where('tindakan_id', $req->tindakan_id_filter);
                }

                if ($req->rekomendasi_tanggal_bedah != '') {
                    $q->where('tanggal_rekomendasi_bedah', $req->rekomendasi_tanggal_bedah);
                }

                if ($req->ruangan_rawat_inap != '') {
                    $q->whereHas('RekamMedisPasien', function ($q) use ($req) {
                        $q->whereHas('KamarRawatInapDanBedahDetail', function ($q) use ($req) {
                            $q->where('kamar_rawat_inap_dan_bedah_id', $req->ruangan_rawat_inap);
                        });
                    });
                }

                if ($req->status != '') {
                    $q->where('status', $req->status);
                }

                if ($req->branch_id != '') {
                    $q->whereHas('RekamMedisPasien', function ($q) use ($req) {
                        $q->whereHas('pendaftaran', function ($q) use ($req) {
                            $q->where('branch_id', $req->branch_id);
                        });
                    });
                }
            })
            ->orderBy('tanggal_rekomendasi_bedah', 'DESC');

        $totalData = $data->count();
        $totalFiltered = 0;
        $search = $req->input('search.value');
        if (!empty($req->input('search.value'))) {
            $data = $data->where(function ($query) use ($search) {

                $query
                    ->Orwhere('tanggal_rekomendasi_bedah', 'ilike', '%' . $search . '%');
            });

            $data = $data->orWhereHas('RekamMedisPasien', function ($query) use ($search) {
                $query->where('kode', 'ilike', "%" . $search . "%");
            });
            $data = $data->orWhereHas('RekamMedisPasien', function ($query) use ($search) {
                $query->WhereHas('pasien', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            });
            $data = $data->orWhereHas('RekamMedisPasien', function ($query) use ($search) {
                $query->WhereHas('pasien', function ($query) use ($search) {
                    $query->WhereHas('owner', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
                });
            });
            $data = $data->orWhereHas('RekamMedisPasien', function ($query) use ($search) {
                $query->where('diagnosa', 'ilike', "%" . $search . "%");
            });

            $data = $data->orWhereHas('RekamMedisPasien', function ($query) use ($search) {
                $query->where('catatan', 'ilike', "%" . $search . "%");
            });

            $data = $data->orWhereHas('Tindakan', function ($query) use ($search) {
                $query->where('name', 'ilike', "%" . $search . "%");
            });

            $data = $data->orWhereHas('RekamMedisPasien', function ($query) use ($search) {
                $query = $query->OrderBy('created_at', 'desc')->limit(1)->WhereHas('KamarRawatInapDanBedahDetail', function ($query) use ($search) {
                    $query = $query->WhereHas('KamarRawatInapDanBedah', function ($query) use ($search) {
                        $query->where('name', 'ilike', "%" . $search . "%");
                    });
                });
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
                if ($post->status == 'Released') {
                    $edit = '';
                    $delete = '';

                    if (Auth::user()->akses('edit')) {
                        $edit = '<li>' .
                        '<a href="javascript:;" onclick="openModal(\'' . $post->rekam_medis_pasien_id . '\',\'' . $post->id . '\')" class="dropdown-item text-warning">' .
                            '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Proses Bedah' .
                            '</a>' .
                            '</li>';
                    }

                    $lihatRekamMedis = '<a href="javascript:;" onclick="lihatRekamMedis(\'' . $post->RekamMedisPasien->pasien_id . '\')" class="dropdown-item text-info">' .
                        '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat Rekam Medis' .
                        '</a>';

                    $formPersetujuan = '<a href="javascript:;"' .
                    'onclick="formPersetujuan(\'' . $post->rekam_medis_pasien_id . '\',\'' . $post->id . '\',\'' . $post->upload_form_persetujuan . '\')"' .
                        'class="dropdown-item text-danger">' .
                        '<i class="fa-solid fa-handshake"></i>&nbsp;&nbsp;&nbsp;Form Persetujuan' .
                        '</a>';

                    $aksi = '<div class="dropdown">' .
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
                } else {
                    $aksi = '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium text-center">Done</div>';
                }
                $nestedData['aksi'] = $aksi;
                $nestedData['icon'] = '<i class="' . $post->icon . ' text-2xl"></i>';
                $nestedData['kode'] = $post->RekamMedisPasien->kode;
                $nestedData['owner'] = $post->RekamMedisPasien->pasien != null ? ($post->RekamMedisPasien->pasien != '' ? $post->RekamMedisPasien->pasien->owner->name : '-') : "-";
                $nestedData['pasien'] = $post->RekamMedisPasien->pasien != null ? $post->RekamMedisPasien->pasien->name : "-";
                $nestedData['dokter_poli'] = $post->CreatedBy != null ? $post->CreatedBy->name : "-";
                $nestedData['bedah'] = $post->Tindakan ? $post->Tindakan->name : "-";
                $nestedData['diagnosa'] = $post->RekamMedisPasien->diagnosa;
                $nestedData['catatan'] = $post->RekamMedisPasien->catatan;
                if (!empty($post->RekamMedisPasien->KamarRawatInapDanBedahDetail->sortBy('created_at')->first()->KamarRawatInapDanBedah)) {
                    $bedah = $post->RekamMedisPasien->KamarRawatInapDanBedahDetail->sortBy('created_at')->first()->KamarRawatInapDanBedah->name;
                } else {
                    $bedah = "";
                }
                $nestedData['ruangan'] = isset($post->RekamMedisPasien->KamarRawatInapDanBedahDetail) ?: "";
                $nestedData['ruangan'] = $bedah;
                $nestedData['sequence'] = '<input type="number" value="' . $post->sequence . '" class="form-control border bg-white text-center" style="color:#c70039" onchange="gantiSequence(\'' . $post->id . '\',this)">';
                $nestedData['image'] = '<img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $post->RekamMedisPasien->Pasien->image . '" alt="No image">';
                $nestedData['status_urgensi'] = $post->status_urgensi == 'true' ? '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Urgent</div>' : '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium"  style="background:blue">Normal</div>';
                $nestedData['tanggal_rekomendasi_bedah'] = Date('d-M-Y', strtotime($post->tanggal_rekomendasi_bedah));
                if ($post->status == 'Released') {
                    $nestedData['status'] = '<div class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Menunggu</div>';
                } elseif ($post->status == 'Rejected') {
                    $nestedData['status'] = '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Cancel By ' . $post->UpdatedBy->nama_panggilan . '</div>';
                } else {
                    $nestedData['status'] = '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium">Done By ' . $post->UpdatedBy->nama_panggilan . '</div>';
                }

                $nestedData['DT_RowIndex'] = $key + 1 + $start;
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

        return $json_data;
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
        $count = (int) $index->id;
        $collect_id = [];
        for ($i = 0; $i < count($collect); $i++) {
            array_push($collect_id, (int) $collect[$i]->id);
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
            $index = (int) $index->id + 1;
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
                },
            ])
            ->where('status', true)
            ->get();

        return view('rawat_inap/bedah/template_resep', compact('req', 'produkObat'));
    }

    public function tambahRacikanChild(Request $req)
    {
        $rekamMedis = $this->model->rekamMedisPasien()->find($req->id);

        $produkObat = $this->model->produkObat()
            ->with([
                'StockFirst' => function ($q) use ($rekamMedis) {
                    $q->where('branch_id', $rekamMedis->Pendaftaran->branch_id);
                },
            ])
            ->where('status', true)
            ->get();
        return view('rawat_inap/bedah/template_racikan_child', compact('req', 'produkObat'));
    }

    public function getRekamMedis(Request $req)
    {
        $rm = $this->model->rekamMedisPasien()
            ->where('id', $req->id)
            ->first();
        if ($rm) {
            $data = $this->model->Pasien()
                ->find($rm->Pasien->id);

            $infoPasien = $this->model->pendaftaran_pasien()
                ->where('pendaftaran_id', $rm->pendaftaran_id)
                ->where('pasien_id', $rm->Pasien->id)
                ->first();
            return view('rawat_inap/bedah/template_data', compact('data', 'rm', 'req', 'infoPasien'));
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
                'created_by' => me(),
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
        $count = (int) $index->id;
        $collect_id = [];
        for ($i = 0; $i < count($collect); $i++) {
            array_push($collect_id, (int) $collect[$i]->id);
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
            $index = (int) $index->id + 1;
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

                        $this->model->rekamMedisDiagnosa()
                            ->create([
                                'rekam_medis_pasien_id' => $req->id,
                                'id' => $idRekamMedisDiagnosa,
                                'diagnosa' => $req->diagnosa,
                                'resource' => 'Bedah',
                                'created_by' => me(),
                                'updated_by' => me(),
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
                                'id' => $idRekamMedisDiagnosa,
                                'catatan' => $req->catatan,
                                'resource' => 'Bedah',
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);
                        $text = '<b>' . Auth::user()->name . '</b> menambahkan catatan <b>' . $req->catatan . '</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_diagnosa', $idRekamMedisDiagnosa);
                        break;
                    case 'catatan-pemeriksaan':
                        $rekamMedisPasien = $this->model->rekamMedisPasien()
                            ->where('id', $req->id)
                            ->update([
                                'catatan' => $req->catatan
                            ]);

                        $text = '<b>' . Auth::user()->name . '</b> merubah catatan pemeriksaan <b>' . $req->catatan . '</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);
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
                        break;
                    case 'upload_form_persetujuan':
                        $file = $req->file('form_persetujuan');
                        if ($file) {
                            $path = 'image/form_persetujuan_bedah';
                            $id = Str::uuid($req->rekam_medis_pasien_id . '-' . $req->rekam_medis_rekomendasi_tindakan_bedah)->toString();
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

                        $this->model->rekamMedisRekomendasiTindakanBedah()
                            ->where('status', 'Released')
                            ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
                            ->update([
                                'upload_form_persetujuan' => $foto
                            ]);

                        $text = '<b>' . Auth::user()->name . '</b> mengupload bukti form persetujuan bedah';
                        $this->addRekamMedisLogHistory($req->rekam_medis_pasien_id, $text, 'mp_rekam_medis_rekomendasi_tindakan_bedah', $req->rekam_medis_pasien_id);
                        break;
                    case 'pakan':
                        $rekamMedisPasien = $this->model->rekamMedisPasien()
                            ->find($req->id);

                        $stock = decreasingStock('NON OBAT', $req->pakan, $rekamMedisPasien->pendaftaran->branch_id, 1, $rekamMedisPasien->kode);

                        $produkObat = $this->model->itemNonObat()->find($req->pakan);
                        if (count($stock->getData()->mutasi) == 0) {
                            DB::rollBack();
                            return Response()->json(['status' => 2, 'message' => 'Stock untuk ' . $produkObat->name . ' sudah habis.']);
                        }

                        
                        // $idJurnal = $this->model->jurnal()->max('id') + 1;
                        $kodeJurnal = generateKodeJurnal($rekamMedisPasien->pendaftaran->branch->kode)->getData()->kode;

                        $this->model->jurnal()
                            ->create([
                                // 'id'    => $idJurnal,
                                'kode'  => $kodeJurnal,
                                'branch_id' => $rekamMedisPasien->pendaftaran->branch_id,
                                'tanggal' => dateStore(),
                                'ref' => $rekamMedisPasien->kode,
                                'jenis' => 'RAWAT INAP',
                                'dk' => 'KREDIT',
                                'description' => 'PENGELUARAN STOCK ' .  $produkObat->name,
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
                            return Response()->json(['status' => 2, 'message' => 'Stock untuk ' . $produkObat->name . ' sudah habis.']);
                        }

                        // $idJurnal = $this->model->jurnal()->max('id') + 1;
                        
                        $kodeJurnal = generateKodeJurnal($rekamMedisPasien->pendaftaran->branch->kode)->getData()->kode;

                        $this->model->jurnal()
                            ->create([
                                // 'id'    => $idJurnal,
                                'kode' => $kodeJurnal,
                                'branch_id' => $rekamMedisPasien->pendaftaran->branch_id,
                                'tanggal' => dateStore(),
                                'ref' => $rekamMedisPasien->kode,
                                'jenis' => 'RAWAT INAP',
                                'dk' => 'KREDIT',
                                'description' => 'PENGELUARAN STOCK ' .  $produkObat->name,
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
                                            'rekam_medis_resep_id'  => $idRekamMedisResep,
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
                    case 'boleh_pulang':
                        $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->id)->first();
                        if (!$check) {
                            DB::rollBack();
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
                                    'status_pemeriksaan' => 'Boleh Pulang',
                                    'updated_by' => me(),
                                    'updated_at' => now(),
                                ]
                            );
                        $this->notify->broadcastingAntrianApotek($req);

                        DB::commit();
                        return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);
                        break;
                    case 'sudah_di_bedah':
                        $check = $this->model->kamarRawatInapDanBedahDetail()->where('rekam_medis_pasien_id', $req->id)->first();
                        if (!$check) {
                            DB::rollBack();
                            return Response()->json(['status' => 2, 'message' => 'Data corrupt, hubungi developer']);
                        }

                        if ($req->id_bedah) {
                            foreach ($req->id_bedah as $key => $value) {
                                $rekamMedisPasien = $this->model->rekamMedisPasien()
                                    ->find($req->id);

                                $this->model->rekamMedisRekomendasiTindakanBedah()
                                    ->where('rekam_medis_pasien_id', $req->id)
                                    ->where('id', $value)
                                    ->where('status', 'Released')
                                    ->update(
                                        [
                                            'status' => $req->input("status_{$value}"),
                                            'updated_by' => me(),
                                            'updated_at' => now(),
                                        ]
                                    );
                            }

                            $text = '<b>' . Auth::user()->name . '</b> telah memproses rekomendasi tindakan bedah.';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', 0);
                            DB::commit();
                            return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);
                        }


                        break;
                    case 'pasien_meninggal':
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
                                    'status_pengambilan_obat' => true,
                                    'tanggal_keluar' => dateStore(),
                                    'updated_by' => me(),
                                    'updated_at' => now(),
                                ]
                            );

                        $this->model->rekamMedisResep()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->where('status_pembuatan_obat', 'Undone')
                            ->delete();

                        $rekamMedisPasien = $this->model->rekamMedisPasien()
                            ->find($req->id);

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
                                'meninggal_saat' => 'Bedah',
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);

                        $this->model->rekamMedisRekomendasiTindakanBedah()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->where('status', 'Released')
                            ->update(
                                [
                                    'status' => 'Cancel',
                                    'updated_by' => me(),
                                    'updated_at' => now(),
                                ]
                            );
                        $text = '<b>' . Auth::user()->name . '</b> mengidentifikasi <b>Pasien telah meninggal</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_pasien', $req->id);
                        DB::commit();
                        return Response()->json(['status' => 1, 'message' => 'Berhasil mengupdate data']);
                        break;
                    case 'di_tolak_bedah':
                        $this->model->rekamMedisRekomendasiTindakanBedah()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->where('id', $req->id_bedah)
                            ->where('status', 'Released')
                            ->update(
                                [
                                    'status' => 'Rejected',
                                    'keterangan' => $req->keterangan,
                                    'updated_by' => me(),
                                    'updated_at' => now(),
                                ]
                            );
                        $text = '<b>' . Auth::user()->name . '</b> menolak rekomendasi tindakan bedah';
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
                        $text = 'Pasien <b>' . $rekamMedisPasien->Pasien->name . '</b> pindah rawat inap dari Ruangan <b>' . $kamarA->KamarRawatInapDanBedah->name . '</b> ke Ruangan <b>' . $kamarB->name . '</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_tindakan', 0);
                        DB::commit();
                        return Response()->json(['status' => 1, 'message' => 'Berhasil memindahkan pasien']);
                        break;
                    case 'obat_bedah';
                        $rekamMedisPasien = $this->model->rekamMedisPasien()
                            ->find($req->id);

                        foreach ($req->parent_resep as $i => $d) {

                            $stock = decreasingStock('OBAT', $req->produk_obat_non_racikan[$i], $rekamMedisPasien->pendaftaran->branch_id, $req->qty_non_racikan[$i], $rekamMedisPasien->kode);
                            $produkObat = $this->model->produkObat()->find($req->produk_obat_non_racikan[$i]);

                            if (count($stock->getData()->mutasi) == 0) {
                                DB::rollBack();
                                return Response()->json(['status' => 2, 'message' => 'Stock untuk ' . $produkObat->name . ' sudah habis.']);
                            }

                            // $idJurnal = $this->model->jurnal()->max('id') + 1;
                            $kodeJurnal = generateKodeJurnal($rekamMedisPasien->pendaftaran->branch->kode)->getData()->kode;

                            $this->model->jurnal()
                                ->create([
                                    // 'id'    => $idJurnal,
                                    'kode' => $kodeJurnal,
                                    'branch_id' => $rekamMedisPasien->pendaftaran->branch_id,
                                    'tanggal' => dateStore(),
                                    'ref' => $rekamMedisPasien->kode,
                                    'jenis' => 'BEDAH',
                                    'dk' => 'KREDIT',
                                    'description' => 'PENGELUARAN STOCK ' .  $produkObat->name,
                                    'nominal' => $stock->getData()->total,
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);

                            $resep = '';
                            $idRekamMedisResep = $this->model->rekamMedisResep()
                                ->where('rekam_medis_pasien_id', $req->id)
                                ->max('id') + 1;

                            $this->model->rekamMedisResep()
                                ->create([
                                    'rekam_medis_pasien_id' => $req->id,
                                    'id' => $idRekamMedisResep,
                                    'produk_obat_id' => $req->produk_obat_non_racikan[$i],
                                    'status_pembuatan_obat' => 'Done',
                                    'status_resep' => 'Bedah',
                                    'jenis_obat' => $d,
                                    'qty' => $req->qty_non_racikan[$i],
                                    'harga_jual' => convertNumber($req->harga_non_racikan),
                                    'description' => $req->description_non_racikan[$i],
                                    'created_by' => me(),
                                    'updated_by' => me(),
                                ]);

                            $produkObat = $this->model->produkObat()->find($req->produk_obat_non_racikan[$i]);
                            $resep = $produkObat->name;
                            $text = '<b>' . Auth::user()->name . '</b> memberi kan resep <b>' . $resep . ' </b>';
                            $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_resep', $idRekamMedisResep);
                        }
                        break;
                    case 'tindakan_bedah':
                        $idRekamMedisTindakan = $this->model->rekamMedisRekomendasiTindakanBedah()
                            ->where('rekam_medis_pasien_id', $req->id)
                            ->max('id') + 1;

                        $tindakan = $this->model->tindakan()->find($req->rekomendasi_tindakan_bedah);

                        $this->model->rekamMedisRekomendasiTindakanBedah()
                            ->create([
                                'rekam_medis_pasien_id' => $req->id,
                                'id' => $idRekamMedisTindakan,
                                'tindakan_id' => $req->rekomendasi_tindakan_bedah,
                                'tanggal_rekomendasi_bedah' => dateStore(),
                                'keterangan' => 'DITAMBAHKAN OLEH DOKTER BEDAH',
                                'status' => 'Done',
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);

                        $this->model->rekamMedisPasien()
                            ->find($req->id)
                            ->update([
                                'tindakan_bedah' => true,
                            ]);
                        $text = '<b>' . Auth::user()->name . '</b> melakukan tindakan bedah <b>' . $tindakan->name . '</b>';
                        $this->addRekamMedisLogHistory($req->id, $text, 'mp_rekam_medis_rekomendasi_tindakan_bedah', $idRekamMedisTindakan);
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

        $data = $this->model->rekamMedisRekomendasiTindakanBedah()
            ->where('rekam_medis_pasien_id', $req->rekam_medis_pasien_id)
            ->where('id', $req->rekam_medis_rekomendasi_tindakan_bedah_id)
            ->first();

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
                    $data = $this->model->rekamMedisHasilLab()
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

    public function bedahExcel(Request $req)
    {
        return Excel::download(new BedahExport($req), 'list_bedah.xlsx');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Modeler;
use App\Models\Poli;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanDokterController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index(Request $req)
    {
        if (!isset($req->tanggal_awal)) {
            $req->tanggal_awal = Carbon::now()->format('Y-m-d');
        }

        if (!isset($req->tanggal_akhir)) {
            $req->tanggal_akhir = Carbon::now()->format('Y-m-d');
        }

        if (!isset($req->branch_id)) {
            $req->branch_id = '';
        }

        $data = $this->model->user()
            ->whereHas('karyawan', function ($q) use ($req) {
                if (request()->filled('bagian_id')) {
                    $q->where('bagian_id', $req->bagian_id);
                }
            })
            ->where(function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    if ($req->branch_id != '') {
                        $q->where('branch_id', $req->branch_id);
                    }
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->whereHas('Role', function ($q) use ($req) {
                $q->where('type_role', 'DOKTER');
                // $q->where('name', '!=', 'Superuser');
            })
            ->withCount([
                'RekamMedisPasien as rawat_jalan' => function ($q) use ($req) {
                    $q->where('rawat_jalan', true);
                    $q->whereDate('created_at', '>=', $req->tanggal_awal);
                    $q->whereDate('created_at', '<=', $req->tanggal_akhir);
                },
                'RekamMedisPasien as rawat_inap' => function ($q) use ($req) {
                    $q->where('rawat_inap', true);
                    $q->whereDate('created_at', '>=', $req->tanggal_awal);
                    $q->whereDate('created_at', '<=', $req->tanggal_akhir);
                },
                'RekamMedisPasien as vaksin' => function ($q) use ($req) {
                    $q->whereHas('RekamMedisTindakan', function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_tindakan.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_tindakan.created_at', '<=', $req->tanggal_akhir);
                        $q->whereHas('Tindakan', function ($q) use ($req) {
                            $q->where('name', 'ilike', '%vaksin%');
                        });
                    });
                },
                'RekamMedisPasien as grooming' => function ($q) use ($req) {
                    $q->whereHas('RekamMedisTindakan', function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_tindakan.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_tindakan.created_at', '<=', $req->tanggal_akhir);
                        $q->whereHas('Tindakan', function ($q) use ($req) {
                            $q->where('name', 'ilike', '%grooming%');
                        });
                    });
                },
                'RekamMedisPasien as steril' => function ($q) use ($req) {
                    $q->whereHas('RekamMedisTindakan', function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_tindakan.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_tindakan.created_at', '<=', $req->tanggal_akhir);
                        $q->whereHas('Tindakan', function ($q) use ($req) {
                            $q->where('name', 'ilike', '%steril%');
                        });
                    });
                },
            ])
            ->get();

        $dokter = $this->model->user()
            ->whereHas('karyawan', function ($q) use ($req) {
                if (request()->filled('bagian_id')) {
                    $q->where('bagian_id', $req->bagian_id);
                }
            })
            ->where(function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    if ($req->branch_id != '') {
                        $q->where('branch_id', $req->branch_id);
                    }
                } else {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->whereHas('Role', function ($q) use ($req) {
                $q->where('type_role', 'DOKTER');
                // $q->where('name', '!=', 'Superuser');
            })
            ->whereHas('RekamMedisRekomendasiTindakanBedah', function ($q) use ($req) {
                $q->whereDate('mp_rekam_medis_rekomendasi_tindakan_bedah.updated_at', '>=', $req->tanggal_awal);
                $q->whereDate('mp_rekam_medis_rekomendasi_tindakan_bedah.updated_at', '<=', $req->tanggal_akhir);
                $q->where('status', 'Done');
            })
            ->get();

        $count = [];
        foreach ($dokter as $key => $value) {
            $count['rekamMedisRekomendasiTindakanBedah as ' . 'bedah_' . $value->id] = function ($q) use ($value, $req) {
                $q->whereDate('mp_rekam_medis_rekomendasi_tindakan_bedah.updated_at', '>=', $req->tanggal_awal);
                $q->whereDate('mp_rekam_medis_rekomendasi_tindakan_bedah.updated_at', '<=', $req->tanggal_akhir);
                $q->where('status', 'Done');
                $q->whereHas('rekamMedisPasien.Pendaftaran', function ($q) use ($req) {
                    if (Auth::user()->akses('global')) {
                        if ($req->branch_id != '') {
                            $q->where('branch_id', $req->branch_id);
                        }
                    } else {
                        $q->where('branch_id', Auth::user()->branch_id);
                    }
                });
                $q->whereHas('rekamMedisPasien', function ($q) use ($req, $value) {
                    $q->whereHas('Pasien', function ($q) use ($value) {
                        $q->whereDoesntHave('pasienMeninggal');
                    });
                });
            };
        }

        $tindakan = $this->model->tindakan()
            // ->where(function ($q) use ($req) {
            //     if ($req->tindakan_id != '') {
            //         $q->where('id', $req->tindakan_id);
            //     }
            // })
            ->where(function ($q) use ($req) {
                $q->whereHas('rekamMedisRekomendasiTindakanBedah', function ($q) use ($req) {
                    $q->whereDate('mp_rekam_medis_rekomendasi_tindakan_bedah.updated_at', '>=', $req->tanggal_awal);
                    $q->whereDate('mp_rekam_medis_rekomendasi_tindakan_bedah.updated_at', '<=', $req->tanggal_akhir);
                    $q->where('status', 'Done');
                    $q->whereHas('UpdatedBy', function ($q) {
                        $q->whereHas('karyawan', function ($q) {
                            if (request()->filled('bagian_id')) {
                                $q->where('bagian_id', request('bagian_id'));
                            }
                        });
                    });
                    $q->whereHas('rekamMedisPasien.Pendaftaran', function ($q) use ($req) {
                        if (Auth::user()->akses('global')) {
                            if ($req->branch_id != '') {
                                $q->where('branch_id', $req->branch_id);
                            }
                        } else {
                            $q->where('branch_id', Auth::user()->branch_id);
                        }
                    });
                    $q->whereHas('rekamMedisPasien', function ($q) use ($req) {
                        $q->whereHas('Pasien', function ($q) {
                            $q->whereDoesntHave('pasienMeninggal');
                        });
                    });
                });
            })
            ->withCount($count)
            ->get();
        $bagian = $this->model->bagian()->where('status', true)->get();
        return view('laporan.laporan_dokter.rekap_laporan_dokter', compact('req', 'data', 'tindakan', 'bagian', 'dokter'));
        // return view('laporan/laporan_pendapatan/laporan_pendapatan', compact('req', 'data', 'tanggal_awal', 'tanggal_akhir'));
    }
}

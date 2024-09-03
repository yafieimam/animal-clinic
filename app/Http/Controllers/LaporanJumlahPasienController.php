<?php

namespace App\Http\Controllers;

use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanJumlahPasienController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index(Request $req)
    {
        if (!isset($req->tanggal_awal)) {
            $req->tanggal_awal = dateStore();
        }

        if (!isset($req->tanggal_akhir)) {
            $req->tanggal_akhir = dateStore();
        }

        if (!isset($req->tindakan_id)) {
            $req->tindakan_id = '';
        }

        if (!isset($req->binatang_id)) {
            $req->binatang_id = '';
        }

        $branch = $this->model->branch()
            ->where(function ($q) use ($req) {
                if (Auth::user()->akses('global')) {
                    if ($req->branch_id != '') {
                        $q->where('id', $req->branch_id);
                    }
                } else {
                    $q->where('id', Auth::user()->branch_id);
                }
            })
            ->withCount(
                [
                    'PendaftaranPasien as total_pasien_daftar' => function ($q) use ($req) {
                        $q->whereDate('qm_pendaftaran_pasien.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('qm_pendaftaran_pasien.created_at', '<=', $req->tanggal_akhir);
                        $q->whereIn('qm_pendaftaran_pasien.status', ['Cancel', 'Sudah Diperiksa']);
                    },
                    'PendaftaranPasien as total_pasien_batal' => function ($q) use ($req) {
                        $q->whereDate('qm_pendaftaran_pasien.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('qm_pendaftaran_pasien.created_at', '<=', $req->tanggal_akhir);
                        $q->where('qm_pendaftaran_pasien.status', 'Cancel');
                        $q->whereHas('Pasien', function ($q) use ($req) {
                            if ($req->binatang_id != '') {
                                $q->where('binatang_id', $req->binatang_id);
                            }
                        });
                    },
                    'PendaftaranPasien as total_pasien_periksa' => function ($q) use ($req) {
                        $q->whereDate('qm_pendaftaran_pasien.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('qm_pendaftaran_pasien.created_at', '<=', $req->tanggal_akhir);
                        $q->where('qm_pendaftaran_pasien.status', 'Sudah Diperiksa');
                        $q->whereHas('Pasien', function ($q) use ($req) {
                            if ($req->binatang_id != '') {
                                $q->where('binatang_id', $req->binatang_id);
                            }
                        });
                    },
                    'rekamMedisPasien as total_rawat_jalan' => function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '<=', $req->tanggal_akhir);
                        $q->where('rawat_inap', false);
                        $q->where(function ($q) {
                            $q->where('rawat_jalan', true);
                            $q->orWhere('grooming', true);
                        });
                        $q->whereHas('Pasien', function ($q) use ($req) {
                            if ($req->binatang_id != '') {
                                $q->where('binatang_id', $req->binatang_id);
                            }
                        });
                    },
                    'rekamMedisPasien as total_rawat_inap' => function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '<=', $req->tanggal_akhir);
                        $q->where('rawat_inap', true);
                        $q->whereHas('Pasien', function ($q) use ($req) {
                            if ($req->binatang_id != '') {
                                $q->where('binatang_id', $req->binatang_id);
                            }
                        });
                    },
                    'rekamMedisPasien as total_pasien_meninggal' => function ($q) use ($req) {
                        $q->whereDate('tanggal_keluar', '>=', $req->tanggal_awal);
                        $q->whereDate('tanggal_keluar', '<=', $req->tanggal_akhir);
                        $q->whereIn('status_kepulangan', ['Pasien Meninggal']);
                        $q->whereHas('Pasien', function ($q) use ($req) {
                            if ($req->binatang_id != '') {
                                $q->where('binatang_id', $req->binatang_id);
                            }
                        });
                    },
                    'rekamMedisPasien as total_pasien_pulang' => function ($q) use ($req) {
                        $q->whereDate('tanggal_keluar', '>=', $req->tanggal_awal);
                        $q->whereDate('tanggal_keluar', '<=', $req->tanggal_akhir);
                        $q->whereIn('status_kepulangan', ['Rekomendasi Dokter', 'Pulang Paksa']);
                        $q->whereHas('Pasien', function ($q) use ($req) {
                            if ($req->binatang_id != '') {
                                $q->where('binatang_id', $req->binatang_id);
                            }
                        });
                    },
                ],
            )
            ->get();

        $hewan = $this->model->binatang()
            ->withCount(
                [
                    'rekamMedisPasien as rawat_jalan' => function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '<=', $req->tanggal_akhir);
                        $q->where('rawat_inap', false);
                        $q->where(function ($q) {
                            $q->where('rawat_jalan', true);
                            $q->orWhere('grooming', true);
                        });
                        $q->whereHas('Pendaftaran', function ($q) use ($req) {
                            if (Auth::user()->akses('global')) {
                                if ($req->branch_id != '') {
                                    $q->where('branch_id', $req->branch_id);
                                }
                            } else {
                                $q->where('branch_id', Auth::user()->branch_id);
                            }
                        });
                    },
                    'rekamMedisPasien as rawat_inap' => function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '<=', $req->tanggal_akhir);
                        $q->where('rawat_inap', true);
                        $q->whereHas('Pendaftaran', function ($q) use ($req) {
                            if (Auth::user()->akses('global')) {
                                if ($req->branch_id != '') {
                                    $q->where('branch_id', $req->branch_id);
                                }
                            } else {
                                $q->where('branch_id', Auth::user()->branch_id);
                            }
                        });
                    },
                    'rekamMedisPasien as titip_sehat' => function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_pasien.created_at', '<=', $req->tanggal_akhir);
                        $q->where('titip_sehat', true);
                        $q->whereHas('Pendaftaran', function ($q) use ($req) {
                            if (Auth::user()->akses('global')) {
                                if ($req->branch_id != '') {
                                    $q->where('branch_id', $req->branch_id);
                                }
                            } else {
                                $q->where('branch_id', Auth::user()->branch_id);
                            }
                        });
                    },
                    'rekamMedisPasien as vaksin' => function ($q) use ($req) {
                        $q->whereHas('Pendaftaran', function ($q) use ($req) {
                            if (Auth::user()->akses('global')) {
                                if ($req->branch_id != '') {
                                    $q->where('branch_id', $req->branch_id);
                                }
                            } else {
                                $q->where('branch_id', Auth::user()->branch_id);
                            }
                        });

                        $q->whereHas('RekamMedisTindakan', function ($q) use ($req) {
                            $q->whereDate('mp_rekam_medis_tindakan.created_at', '>=', $req->tanggal_awal);
                            $q->whereDate('mp_rekam_medis_tindakan.created_at', '<=', $req->tanggal_akhir);
                            $q->whereHas('Tindakan', function ($q) use ($req) {
                                $q->where('name', 'ilike', '%vaksin%');
                            });
                        });
                    },
                    'rekamMedisPasien as grooming' => function ($q) use ($req) {
                        $q->whereHas('Pendaftaran', function ($q) use ($req) {
                            if (Auth::user()->akses('global')) {
                                if ($req->branch_id != '') {
                                    $q->where('branch_id', $req->branch_id);
                                }
                            } else {
                                $q->where('branch_id', Auth::user()->branch_id);
                            }
                        });
                        // $q->where('grooming', true);
                        $q->whereHas('RekamMedisTindakan', function ($q) use ($req) {
                            $q->whereDate('mp_rekam_medis_tindakan.created_at', '>=', $req->tanggal_awal);
                            $q->whereDate('mp_rekam_medis_tindakan.created_at', '<=', $req->tanggal_akhir);
                            $q->whereHas('Tindakan', function ($q) use ($req) {
                                $q->where('name', 'ilike', '%grooming%');
                            });
                        });
                    },
                    'rekamMedisPasien as bedah' => function ($q) use ($req) {
                        $q->whereHas('Pendaftaran', function ($q) use ($req) {
                            if (Auth::user()->akses('global')) {
                                if ($req->branch_id != '') {
                                    $q->where('branch_id', $req->branch_id);
                                }
                            } else {
                                $q->where('branch_id', Auth::user()->branch_id);
                            }
                        });

                        $q->whereHas('RekamMedisRekomendasiTindakanBedah', function ($q) use ($req) {
                            $q->whereDate('mp_rekam_medis_rekomendasi_tindakan_bedah.updated_at', '>=', $req->tanggal_awal);
                            $q->whereDate('mp_rekam_medis_rekomendasi_tindakan_bedah.updated_at', '<=', $req->tanggal_akhir);
                            $q->where('status', 'Done');
                        });
                    },
                ],
            )
            ->get();

        foreach (binatang() as $key => $value) {

            $count['rekamMedisTindakan as ' . 'tindakan_' . $value->id] = function ($q) use ($value, $req) {
                $q->whereDate('mp_rekam_medis_tindakan.created_at', '>=', $req->tanggal_awal);
                $q->whereDate('mp_rekam_medis_tindakan.created_at', '<=', $req->tanggal_akhir);
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
                        $q->where('binatang_id', $value->id);
                        $q->whereDoesntHave('pasienMeninggal');
                    });
                });
            };


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
                        $q->where('binatang_id', $value->id);
                        $q->whereDoesntHave('pasienMeninggal');
                    });
                });
            };
        }

        $tindakan = $this->model->tindakan()
            ->where(function ($q) use ($req) {
                if ($req->binatang_id != '') {
                    $q->where('binatang_id', $req->binatang_id);
                }

                if ($req->tindakan_id != '') {
                    $q->where('id', $req->tindakan_id);
                }
            })
            ->where(function ($q) use ($req) {
                $q->where(function ($q) use ($req) {
                    $q->whereHas('rekamMedisRekomendasiTindakanBedah', function ($q) use ($req) {
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
                        $q->whereHas('rekamMedisPasien', function ($q) use ($req) {
                            $q->whereHas('Pasien', function ($q) {
                                $q->whereDoesntHave('pasienMeninggal');
                            });
                        });
                    });
                });
                $q->orWhere(function ($q) use ($req) {
                    $q->whereHas('rekamMedisTindakan', function ($q) use ($req) {
                        $q->whereDate('mp_rekam_medis_tindakan.created_at', '>=', $req->tanggal_awal);
                        $q->whereDate('mp_rekam_medis_tindakan.created_at', '<=', $req->tanggal_akhir);
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
                });
            })
            ->withCount($count)
            ->get();
        return view('management_pasien/laporan_jumlah_pasien/laporan_jumlah_pasien', compact('req', 'branch', 'hewan', 'tindakan'));
    }
}

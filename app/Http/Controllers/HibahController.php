<?php

namespace App\Http\Controllers;

use App\Exports\HibahExport;
use Illuminate\Http\Request;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;

class HibahController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('transaksi/hibah/hibah');
    }

    public function aksi($data)
    {

        $cetak =  '<li>' .
            '<a href="javascript:;" onclick="printInvoice(\'' . $data->kasir_id . '\')" class="dropdown-item text-success">' .
            '<i class="fa-solid fa-print"></i>&nbsp;&nbsp;&nbsp;Cetak Invoice' .
            '</a>' .
            '</li>';
        $lihatPembayaran = '<li>' .
            '<a href="javascript:;" onclick="lihatPembayaran(\'' . $data->kasir_id . '\')" class="dropdown-item text-info">' .
            '<i class="fa-solid fa-id-card"></i>&nbsp;&nbsp;&nbsp;Lihat Pembayaran' .
            '</a>' .
            '</li>';

        return '<div class="dropdown">' .
            '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
            '<span class="w-5 h-5 flex items-center justify-center">' .
            '<i class="fa fa-bars"></i>' .
            '</span>' .
            '</button>' .
            '<div class="dropdown-menu w-52 ">' .
            '<ul class="dropdown-content">' .
            $cetak .
            $lihatPembayaran .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model->kasirPembayaran()
            ->with(['kasir'])
            ->where(function ($q) use ($req) {
                if (!Auth::user()->akses('global', null, false)) {
                    // $q->where('branch_id', Auth::user()->branch_id);
                    $q->whereHas('kasir', function ($subQ) use ($req) {
                        $subQ->where('branch_id', Auth::user()->branch_id);
                    });
                } else {
                    if ($req->branch_id != '') {
                        // $q->where('branch_id', $req->branch_id);
                        $q->whereHas('kasir', function ($subQ) use ($req) {
                            $subQ->where('branch_id', $req->branch_id);
                        });
                    }
                }

                if ($req->tanggal_awal != '') {
                    $q->whereDate('updated_at', '>=', $req->tanggal_awal);
                    // $q->whereHas('kasirPembayaran', function ($subQ) use ($req) {
                    //     $subQ->whereDate('created_at', '>=', $req->tanggal_awal);
                    // });
                }

                if ($req->tanggal_akhir != '') {
                    $q->whereDate('updated_at', '<=', $req->tanggal_akhir);
                    // $q->whereHas('kasirPembayaran', function ($subQ) use ($req) {
                    //     $subQ->whereDate('created_at', '<=', $req->tanggal_akhir);
                    // });
                }

                if ($req->owner_id != '') {
                    // $q->where('owner_id', $req->owner_id);
                    $q->whereHas('kasir', function ($subQ) use ($req) {
                        $subQ->where('owner_id', $req->owner_id);
                    });
                }

                if ($req->type_kasir != '') {
                    // $q->where('type_kasir', $req->type_kasir);
                    $q->whereHas('kasir', function ($subQ) use ($req) {
                        $subQ->where('type_kasir', $req->type_kasir);
                    });
                }
            })
            // ->whereNotIn('type_kasir', ['Normal', 'Cicilan', 'Diskon Cicilan', 'Langsung Lunas'])
            // ->where('type_kasir', '!=', 'Normal') 
            // ->whereHas('kasirPembayaran', function ($subQ) use ($req) {
            //     $subQ->whereDate('jenis_pembayaran', 'HIBAH');
            // })
            ->where('jenis_pembayaran', 'HIBAH')
            ->get();

            // dd($data);

            $total_lunas = $data->sum('nilai_pembayaran');
            // $total_lunas = $data->sum(function ($item) {
            //     return $item->kasir->pembayaran ?? 0;
            // });

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            ->addColumn('kode', function ($data) {
                return $data->kasir->kode;
            })
            ->addColumn('branch', function ($data) {
                return $data->kasir->Branch != null ? $data->kasir->Branch->lokasi  : "-";
            })
            ->addColumn('nama_owner', function ($data) {
                return $data->kasir->nama_owner;
            })
            ->addColumn('kode_registrasi', function ($data) {
                return $data->kasir->owner->kode;
            })
            ->addColumn('type_kasir', function ($data) {
                return $data->kasir->type_kasir;
            })
            ->addColumn('total_bayar', function ($data) {
                return $data->nilai_pembayaran;
            })
            ->addColumn('tanggal_buat', function ($data) {
                // return $data->tanggal;
                // return CarbonParse($data->tanggal, 'd-M-Y');
                return CarbonParse($data->kasir->created_at, 'd-M-Y');
            })
            ->addColumn('updated_at', function ($data) {
                // return $data->tanggal;
                // return CarbonParse($data->tanggal, 'd-M-Y');
                return CarbonParse($data->updated_at, 'd-M-Y H:i');
            })
            ->addColumn('status_pembayaran', function ($data) {
                if ($data->kasir->sisa_pelunasan > 0) {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Belum Lunas</div>';
                } elseif ($data->kasir->sisa_pelunasan == 0) {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Lunas</div>';
                }
            })
            ->addColumn('catatan_kasir', function ($data) {
                if ($data->catatan_kasir != 0) {
                    return $data->catatan_kasir;
                } elseif ($data->catatan_kasir == null) {
                    return '-';
                }
            })
            ->addColumn('metode_pembayaran', function ($data) {
                if ($data->kasir->metode_pembayaran == 'TUNAI') {
                    return '<span class="badge badge-info">TUNAI</span>';
                } else {
                    $bank = $data->kasir->nama_bank . '<br>' .
                        'No. Rekening ' . $data->kasir->nomor_kartu . '<br>' .
                        'No. Transaksi ' . $data->kasir->nomor_transaksi;

                    return $bank;
                }
            })
            ->addColumn('total_lunas', function ($data) use ($total_lunas) {
                return $total_lunas;
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'metode_pembayaran', 'status_pembayaran', 'type_kasir', 'catatan_kasir'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('dmY');
        $kode = 'DEPO-'  . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->kasir()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->kasir()
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
                // DB::statement('LOCK TABLE t_jurnal, t_kasir_pembayaran IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                $input = $req->all();
                unset($input['_token']);
                $kasir = $this->model->kasir()->find($req->id);
                $pembayaranSebelumnya =  $this->model->KasirPembayaran()->where('kasir_id', $kasir->id)->sum('nilai_pembayaran');
                $nilaiSelanjutnya = $pembayaranSebelumnya + convertNumber($req->nilai_pembayaran);
                $nilaiPembayaran = convertNumber($req->nilai_pembayaran);

                if ($nilaiSelanjutnya > $kasir->sisa_pelunasan) {
                    $nilaiSelanjutnya = $kasir->sisa_pelunasan - $pembayaranSebelumnya;
                    DB::rollBack();
                    return Response()->json(['status' => 3, 'message' => 'Mengkalkulasi ulang sisa hutang. klik simpan sekali lagi', 'nilai' => $nilaiSelanjutnya, 'sisa' => $nilaiSelanjutnya]);
                }

                if ($nilaiPembayaran > 0) {
                    // $idJurnal = $this->model->jurnal()->max('id') + 1;
                    $kodeJurnal = generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode;

                    $this->model->jurnal()
                        ->create([
                            // 'id'    => $idJurnal,
                            'kode'  => $kodeJurnal,
                            'branch_id' => Auth::user()->branch_id,
                            'tanggal'   => dateStore($req->tanggal),
                            'ref'   => $kasir->kode,
                            'jenis'   => 'KASIR',
                            'dk'    => 'DEBET',
                            'description'    => 'PEMBAYARAN INVOICE ' . $kasir->kode . ' TANGGAL ' . CarbonParse(now(), 'd/m/Y'),
                            'nominal'   => $nilaiPembayaran,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    $this->model->kasirPembayaran()
                        ->create([
                            'kasir_id'   => $kasir->id,
                            'id'     => $this->model->kasirPembayaran()->where('kasir_id', $kasir->id)->max('id') + 1,
                            'ref'    => $kasir->kode,
                            'nilai_pembayaran'   => $nilaiPembayaran,
                            'keterangan'     => $req->keterangan,
                            'jenis_pembayaran' => $req->jenis_pembayaran,
                            'nama_bank' => $req->jenis_pembayaran == 'NON TUNAI' ? $req->nama_bank : null,
                            'nomor_kartu' => $req->jenis_pembayaran == 'NON TUNAI' ? $req->nomor_kartu : null,
                            'nomor_transaksi' => $req->jenis_pembayaran == 'NON TUNAI' ? $req->nomor_transaksi : null,
                            'created_by'     => me(),
                            'updated_by'     => me(),
                        ]);
                }

                $pembayaran =  $this->model->KasirPembayaran()->where('kasir_id', $kasir->id)->sum('nilai_pembayaran');
                $this->model->kasir()
                    ->find($kasir->id)
                    ->update([
                        'sisa_pelunasan' => ($kasir->pembayaran - $pembayaran)
                    ]);

                DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Berhasil menyimpan data']);
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->kasir()->where('id', $req->id)
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

        $data = $this->model->kasir()->where('id', $req->id)->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function getHistorisPembayaran(Request $req)
    {
        $data = $this->model->KasirPembayaran()
            ->where('kasir_id', $req->id)
            ->get();

        return view('transaksi/cicilan/historis_pembayaran', compact('data'));
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->kasir()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function HibahExcel(Request $req)
    {
        return Excel::download(new HibahExport($req), 'Export Hibah_' . CarbonParse(now(), 'd-M-Y') . '.xlsx');
    }
}

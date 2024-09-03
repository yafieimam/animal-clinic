<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modeler;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CicilanExport;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Intervention\Image\ImageManagerStatic as Image;

class CicilanController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('transaksi/cicilan/cicilan');
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';

        if (Auth::user()->akses('edit')) {
            $edit = '<li>' .
                '<a href="javascript:;" onclick="edit(\'' . $data->id . '\')" class="dropdown-item text-success">' .
                '<i class="fa fa-dollar"></i>&nbsp;&nbsp;&nbsp;Pembayaran' .
                '</a>' .
                '</li>';
        }


        $lihatPembayaran = '<li>' .
            '<a href="javascript:;" onclick="lihatPembayaran(\'' . $data->id . '\')" class="dropdown-item text-info">' .
            '<i class="fa-solid fa-id-card"></i>&nbsp;&nbsp;&nbsp;Lihat Pembayaran' .
            '</a>' .
            '</li>';

        $print =
            '<li>' .
            '<a href="javascript:;" onclick="window.open(\'' . route('printPembayaran') . '?id=' . $data->id . '\')"' .
            'class="dropdown-item text-info">' .
            '<i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Print Invoice' .
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
            $edit .
            $lihatPembayaran .
            $print .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model->kasir()
            ->where('sisa_pelunasan', '!=', '0')
            ->where(function ($q) use ($req) {
                if ($req->owner_id != '') {
                    $q->where('owner_id', $req->owner_id);
                }

                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }

                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if ($req->type_kasir != '') {
                    $q->where('type_kasir', $req->type_kasir);
                }

                if ($req->tanggal_awal != '') {
                    $q->whereDate('updated_at', '>=', $req->tanggal_awal);
                }else{
                    $q->whereDate('updated_at', '>=', date('Y-m-d'));
                }

                if ($req->tanggal_akhir != '') {
                    $q->whereDate('updated_at', '<=', $req->tanggal_akhir);
                }else{
                    $q->whereDate('updated_at', '<=', date('Y-m-d'));
                }
            })
            ->orderBy('updated_at', 'DESC')
            // ->take(20)
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
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
            ->addColumn('pembayaran', function ($data) {
                return 'Rp.' . ' ' . number_format($data->pembayaran);
            })
            ->addColumn('catatan_kasir', function ($data) {
                if ($data->catatan_kasir != 0) {
                    return $data->catatan_kasir;
                } elseif ($data->catatan_kasir == null) {
                    return '-';
                }
            })
            ->addColumn('branch', function ($data) {
                return $data->branch ? $data->branch->lokasi : '';
            })
            ->addColumn('sisa_pelunasan', function ($data) {
                return number_format($data->sisa_pelunasan);
            })
            ->addColumn('owner', function ($data) {
                return $data->owner ? $data->owner->name : '';
            })
            ->addColumn('no_registrasi', function ($data) {
                return $data->owner ? $data->owner->kode : '';
            })
            ->addColumn('telpon', function ($data) {
                return $data->owner ? $data->owner->telpon : '';
            })
            ->addColumn('created_at', function ($data) {
                return CarbonParse($data->created_at, 'd-M-Y H:i');
            })
            ->addColumn('updated_at', function ($data) {
                return CarbonParse($data->updated_at, 'd-M-Y H:i');
            })
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy ? $data->CreatedBy->name : '';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence'])
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
                // DB::statement('LOCK TABLE t_jurnal, t_kasir_pembayaran IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                $input = $req->all();
                unset($input['_token']);
                $kasir = $this->model->kasir()->find($req->id);
                $pembayaranSebelumnya =  $this->model->KasirPembayaran()->where('kasir_id', $kasir->id)->sum('nilai_pembayaran');
                $nilaiPembayaran = convertNumber($req->nilai_pembayaran);
                $nilaiSelanjutnya = $pembayaranSebelumnya + $nilaiPembayaran;
                $maxId = $this->model->kasirPembayaran()->where('kasir_id', $kasir->id)->max('id') + 1;

                if ($nilaiSelanjutnya > $kasir->pembayaran) {
                    $nilaiSelanjutnya = $kasir->pembayaran - $pembayaranSebelumnya;
                    DB::rollBack();
                    return Response()->json(['status' => 3, 'message' => 'Mengkalkulasi ulang sisa hutang. klik simpan sekali lagi', 'nilai' => $nilaiSelanjutnya, 'sisa' => $nilaiSelanjutnya]);
                }

                $file = $req->file('bukti_transfer');

                if ($file != null) {
                    $path = 'image/bukti_transfer_pembayaran';
                    $id = Str::uuid($kasir->id)->toString();
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

                    $bukti_transfer = $foto;
                }else{
                    $bukti_transfer = null;
                }

                if ($nilaiPembayaran > 0) {
                    if($req->kategori_pembayaran != 'Hibah'){
                        // $idJurnal = $this->model->jurnal()->max('id') + 1;
                        $kodeJurnal = generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode;
                        // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);

                        $this->model->jurnal()
                            ->create([
                                // 'id'    => $idJurnal,
                                'kode'  => $kodeJurnal,
                                // 'branch_id' => Auth::user()->branch_id,
                                'branch_id' => $kasir->branch_id,
                                'tanggal'   => dateStore($req->tanggal),
                                'ref'   => $kasir->kode,
                                'jenis'   => 'KASIR',
                                'dk'    => 'DEBET',
                                'description'    => 'PEMBAYARAN INVOICE ' . $kasir->kode . ' TANGGAL ' . CarbonParse(now(), 'd/m/Y'),
                                'nominal'   => $nilaiPembayaran,
                                'metode_pembayaran'   => $req->jenis_pembayaran,
                                'nama_bank' => $req->jenis_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                                'nomor_kartu' => $req->jenis_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                                'created_by'    => me(),
                                'updated_by'    => me(),
                            ]);
                    }

                    $this->model->kasirPembayaran()
                        ->create([
                            'kasir_id'   => $kasir->id,
                            'id'     => $maxId,
                            'ref'    => $kasir->kode,
                            'nilai_pembayaran'   => $nilaiPembayaran,
                            'diskon_cicilan' => convertNumber($req->diskon_cicilan),
                            'keterangan'     => $req->keterangan,
                            'jenis_pembayaran' => $req->kategori_pembayaran == 'Hibah' ? 'HIBAH' : $req->jenis_pembayaran,
                            'nama_bank' => $req->jenis_pembayaran != 'TUNAI' ? $req->nama_bank : null,
                            'nomor_kartu' => $req->jenis_pembayaran != 'TUNAI' ? $req->nomor_kartu : null,
                            'nomor_transaksi' => $req->jenis_pembayaran != 'TUNAI' ? $req->nomor_transaksi : null,
                            'bukti_transfer' => $bukti_transfer,
                            'created_by'     => me(),
                            'updated_by'     => me(),
                        ]);
                }

                $pembayaran =  $this->model->KasirPembayaran()->where('kasir_id', $kasir->id)->sum('nilai_pembayaran');
                $diskon_cicilan =  $this->model->KasirPembayaran()->where('kasir_id', $kasir->id)->sum('diskon_cicilan');
                $this->model->kasir()
                    ->find($kasir->id)
                    ->update([
                        'sisa_pelunasan' => ($kasir->pembayaran - $pembayaran - $diskon_cicilan),
                        'type_kasir' => $req->kategori_pembayaran,
                        'langsung_lunas' => $req->kategori_pembayaran != 'Langsung Lunas' ? true : false,
                        'updated_at' => now(),
                        'updated_by'     => me(),
                    ]);
            
                DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Berhasil menyimpan data', 'kasir_id' => $kasir->id, 'id' => $maxId]);
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

    public function editCicilanPembayaran(Request $req)
    {
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }

        $data = $this->model
            ->kasir()
            ->with(['kasirPembayaran' => function ($q) use ($req) {
                $q->where('id', $req->id);
            }])
            ->where('id', $req->kasir_id)
            ->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function getHistorisPembayaran(Request $req)
    {
        $data = $this->model->KasirPembayaran()
            ->where('kasir_id', $req->id)
            ->get();
        $kasir = $this->model->kasir()
            ->find($req->id);
        return view('transaksi/cicilan/historis_pembayaran', compact('data', 'kasir'));
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

    public function printBuktiPembayaran(Request $req)
    {
        $data = $this->model->KasirPembayaran()
            ->where('kasir_id', $req->kasir_id)
            ->where('id', $req->id)
            ->first();

        $nama = 'PEMBAYARAN CICILAN ' . $data->Kasir->kode . '-' . Carbon::parse($data->created_at)->format('Y-m-d') . '.pdf';

        $totalHutang = $data->Kasir->pembayaran - $this->model->KasirPembayaran()
            ->where('kasir_id', $req->kasir_id)
            ->where('id', '<', $req->id)
            ->sum('nilai_pembayaran');
        $pdf = Pdf::loadView('transaksi/cicilan/print', compact('data', 'totalHutang'))
            ->setPaper('a4', 'potrait');
        return $pdf->stream($nama);
    }

    public function CicilanExcel(Request $req)
    {
        return Excel::download(new CicilanExport($req), 'cicilan_' . CarbonParse(now(), 'd-M-Y') . '.xlsx');
    }

    public function uploadCicilanPembayaran(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $kasirPembayaran = $this->model->kasirPembayaran()
                ->where('kasir_id', $req->kasir_id_bukti_transfer)
                ->where('id', $req->id_bukti_transfer)
                ->first();

            $file = $req->file('bukti_transfer_bukti');

            if ($file != null) {
                $path = 'image/bukti_transfer_pembayaran';
                $id = Str::uuid($req->id_bukti_transfer)->toString();
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

                $bukti_transfer = $foto;
            }else{
                $bukti_transfer = null;
            }

            $this->model
                ->kasirPembayaran()
                ->where('kasir_id', $req->kasir_id_bukti_transfer)
                ->where('id', $req->id_bukti_transfer)
                ->update([
                    'bukti_transfer' => $bukti_transfer,
                    'updated_at' => now()
                ]);

            return Response()->json(['status' => 1, 'message' => 'Berhasil menyimpan data']);
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\RekapInvoiceExport;
use Illuminate\Http\Request;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Intervention\Image\ImageManagerStatic as Image;

class RekapInvoiceController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('transaksi/rekap_invoice/rekap_invoice');
    }

    public function aksi($data)
    {
        $edit = '';

        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2){
            $edit = '<li>' . '<a href="javascript:;" onclick="edit(\'' . $data->id . '\')" class="dropdown-item text-warning">' . '<i class="fa-solid fa-pencil"></i>&nbsp;&nbsp;&nbsp;Edit' . '</a>' . '</li>';
        }

        $cetak = '<li>' . '<a href="javascript:;" onclick="printInvoice(\'' . $data->id . '\')" class="dropdown-item text-success">' . '<i class="fa-solid fa-print"></i>&nbsp;&nbsp;&nbsp;Cetak Invoice' . '</a>' . '</li>';
        $lihatPembayaran = '<li>' . '<a href="javascript:;" onclick="lihatPembayaran(\'' . $data->id . '\')" class="dropdown-item text-info">' . '<i class="fa-solid fa-id-card"></i>&nbsp;&nbsp;&nbsp;Lihat Pembayaran' . '</a>' . '</li>';
        $uploadBukti = '<li>' . '<a href="javascript:;" onclick="uploadBukti(\'' . $data->id . '\')" class="dropdown-item text-danger">' . '<i class="fa-solid fa-upload"></i>&nbsp;&nbsp;&nbsp;Upload Bukti Transfer' . '</a>' . '</li>';

        // return '<div class="dropdown">' . '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' . '<span class="w-5 h-5 flex items-center justify-center">' . '<i class="fa fa-bars"></i>' . '</span>' . '</button>' . '<div class="dropdown-menu w-52 ">' . '<ul class="dropdown-content">' . $cetak . $lihatPembayaran . (($data->metode_pembayaran == "TRANSFER" && $data->bukti_transfer == NULL && $data->sisa_pelunasan > 0) ? $uploadBukti : '') . '</ul>' . '</div>' . '</div>';
        return '<div class="dropdown">' . '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' . '<span class="w-5 h-5 flex items-center justify-center">' . '<i class="fa fa-bars"></i>' . '</span>' . '</button>' . '<div class="dropdown-menu w-52 ">' . '<ul class="dropdown-content">' . $edit . $cetak . $lihatPembayaran . (($data->metode_pembayaran != "TUNAI" && $data->bukti_transfer == NULL) ? $uploadBukti : '') . '</ul>' . '</div>' . '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model
            ->kasir()
            ->where(function ($q) use ($req) {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->where('branch_id', Auth::user()->branch_id);
                } else {
                    if ($req->branch_id != '') {
                        $q->where('branch_id', $req->branch_id);
                    }
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

                if ($req->owner_id != '') {
                    $q->where('owner_id', $req->owner_id);
                }

                if ($req->sisa_pelunasan != '') {
                    if ($req->sisa_pelunasan == 1) {
                        $q->where('sisa_pelunasan', 0);
                    } else {
                        $q->where('sisa_pelunasan', '>', 0);
                    }
                }
            })
            ->where(function($q){
                $q->orWhere('type_kasir', '=' , 'Normal');
                $q->orWhere('type_kasir', '=' , 'Diskon Cicilan');
                $q->orWhere('type_kasir', '=' , 'Cicilan');
                $q->orWhere('type_kasir', '=' , 'Langsung Lunas');
            })
            ->get();
        $total_lunas = $data->where('sisa_pelunasan', 0)->sum('pembayaran');
        // $total_lunas = $this->model->KasirPembayaran()
        //     ->where('created_at', '>=', date('Y-m-d'))
        // ->where('keterangan', 'NOT LIKE', '%Rescue%')
        // ->sum('nilai_pembayaran');

        $total_hutang = $data->where('sisa_pelunasan', '>', 0)->sum('sisa_pelunasan');

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            // ->filterColumn('branch', function ($q, $kw) {
            //     $q->whereHas('branch', function ($q) use ($kw) {
            //         $q->where(DB::raw("UPPER(CONCAT(kode,' ',lokasi))"), 'ilike', '%' . strtoupper($kw) . '%');
            //     });
            // })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->lokasi : '-';
            })
            ->addColumn('tanggal_buat', function ($data) {
                // return $data->tanggal;
                return CarbonParse($data->tanggal, 'd-M-Y');
            })
            ->addColumn('updated_at', function ($data) {
                // return $data->tanggal;
                return CarbonParse($data->updated_at, 'd-M-Y H:i');
            })
            ->addColumn('status_pembayaran', function ($data) {
                if ($data->sisa_pelunasan > 0) {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Belum Lunas</div>';
                } elseif ($data->sisa_pelunasan == 0) {
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
                if ($data->metode_pembayaran == 'TUNAI') {
                    return '<span class="badge badge-info">TUNAI</span>';
                } elseif ($data->metode_pembayaran == 'TRANSFER') {
                        return '<span class="badge badge-info">TRANSFER</span>';
                } elseif ($data->metode_pembayaran == 'DEBET') {
                    return '<span class="badge badge-info">DEBET</span>';
                }
            })
            ->addColumn('status_transfer', function ($data) {
                if ($data->bukti_transfer == NULL && $data->metode_pembayaran == 'TRANSFER') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Belum Verifikasi</div>';
                } elseif ($data->bukti_transfer != NULL) {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium">Sudah Verifikasi</div>';
                }

                if ($data->bukti_transfer == NULL && $data->metode_pembayaran == 'DEBET') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Belum Verifikasi</div>';
                } elseif ($data->bukti_transfer != NULL) {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium">Sudah Verifikasi</div>';
                }

                if ($data->bukti_transfer == NULL && $data->metode_pembayaran == 'TUNAI') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">-</div>';
                } elseif ($data->bukti_transfer != NULL) {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium">-</div>';
                }
            })
            ->addColumn('pembayaran', function ($data) {
                return $data->pembayaran - $data->sisa_pelunasan;
                // return $data->kapasitas - $data->KamarRawatInapDanBedahDetail->where('status', 'In Use')->count();
            })
            ->addColumn('dokter', function ($data) {
                $html = '<table class="w-full">';
                $collection = $data->KasirDetail->collect();
                $collection = $collection->unique('RekamMedisPasien.CreatedBy.name');
                foreach ($collection as $key => $value) {
                    if ($value->RekamMedisPasien) {
                        $html .= '<tr><td>' . $value->RekamMedisPasien->CreatedBy->name . '</td></tr>';
                    }
                }
                
                return $html;
                // return $data->kapasitas - $data->KamarRawatInapDanBedahDetail->where('status', 'In Use')->count();
            })
            ->addColumn('status_pembayaran_custom', function ($data) {
                if ($data->sisa_pelunasan > 0) {
                    return 0; // belum lunas
                } elseif ($data->sisa_pelunasan == 0) {
                    return 1; // lunas
                }
            })
            ->addColumn('total_hutang', function ($data) use ($total_hutang) {
                return $total_hutang;
            })
            ->addColumn('total_lunas', function ($data) use ($total_lunas) {
                return $total_lunas;
            })
            ->addColumn('bukti_transfer', function ($data) {
                if ($data->bukti_transfer != null) {
                    // return '<a href="' . url('/') . '/' . $data->bukti_transfer . '" download="' . $data->bukti_transfer . '"><img style="width:100px;height:100px;object-fit:cover;cursor:pointer" src="' . url('/') . '/' . $data->bukti_transfer . '" alt="No image"></a>';
                    return '<a href="' . url('/') . '/' . $data->bukti_transfer . '" target="_blank"><img style="width:100px;height:100px;object-fit:cover;cursor:pointer" src="' . url('/') . '/' . $data->bukti_transfer . '" alt="No image"></a>';
                } else {
                    return "-";
                }
            })
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy ? $data->CreatedBy->name : '-';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'metode_pembayaran', 'status_pembayaran', 'catatan_kasir', 'bukti_transfer', 'pembayaran', 'dokter','status_transfer','created_by'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('dmY');
        $kode = 'DEPO-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model
            ->kasir()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model
            ->kasir()
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

        $index = str_pad($index, 4, '0', STR_PAD_LEFT);

        $kode = $kode . $index;

        return Response()->json(['status' => 1, 'kode' => $kode]);
    }

    public function generateKodeJurnal($branchKode)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'JR-' . $branchKode . '-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model
            ->jurnal()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model
            ->jurnal()
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
                $pembayaranSebelumnya = $this->model
                    ->KasirPembayaran()
                    ->where('kasir_id', $kasir->id)
                    ->sum('nilai_pembayaran');
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

                    $this->model->jurnal()->create([
                        // 'id' => $idJurnal,
                        'kode' => $kodeJurnal,
                        'branch_id' => Auth::user()->branch_id,
                        'tanggal' => dateStore($req->tanggal),
                        'ref' => $kasir->kode,
                        'jenis' => 'KASIR',
                        'dk' => 'DEBET',
                        'description' => 'PEMBAYARAN INVOICE ' . $kasir->kode . ' TANGGAL ' . CarbonParse(now(), 'd/m/Y'),
                        'nominal' => $nilaiPembayaran,
                        'created_by' => me(),
                        'updated_by' => me(),
                    ]);

                    $this->model->kasirPembayaran()->create([
                        'kasir_id' => $kasir->id,
                        'id' =>
                        $this->model
                            ->kasirPembayaran()
                            ->where('kasir_id', $kasir->id)
                            ->max('id') + 1,
                        'ref' => $kasir->kode,
                        'nilai_pembayaran' => $nilaiPembayaran,
                        'keterangan' => $req->keterangan,
                        'jenis_pembayaran' => $req->jenis_pembayaran,
                        'nama_bank' => $req->jenis_pembayaran == 'NON TUNAI' ? $req->nama_bank : null,
                        'nomor_kartu' => $req->jenis_pembayaran == 'NON TUNAI' ? $req->nomor_kartu : null,
                        'nomor_transaksi' => $req->jenis_pembayaran == 'NON TUNAI' ? $req->nomor_transaksi : null,
                        'created_by' => me(),
                        'updated_by' => me(),
                    ]);
                }

                $pembayaran = $this->model
                    ->KasirPembayaran()
                    ->where('kasir_id', $kasir->id)
                    ->sum('nilai_pembayaran');
                $this->model
                    ->kasir()
                    ->find($kasir->id)
                    ->update([
                        'sisa_pelunasan' => $kasir->pembayaran - $pembayaran,
                    ]);

                DB::commit();
                
                return Response()->json(['status' => 1, 'message' => 'Berhasil menyimpan data']);
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }
        });
    }

    public function update(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);
            $kasir = $this->model->kasir()->find($req->id);
            $jurnal = $this->model->jurnal()->where('ref', $kasir->kode)->where('tanggal', $kasir->tanggal)->first();
            if($jurnal){
                DB::table('t_jurnal')
                    ->where('ref', $kasir->kode)
                    ->where('tanggal', $kasir->tanggal)
                    ->update([
                        'metode_pembayaran' => $req->metode_pembayaran,
                        'nama_bank' => ($req->metode_pembayaran == 'TUNAI') ? null : $req->nama_bank,
                        'nomor_kartu' => ($req->metode_pembayaran == 'TUNAI') ? null : $req->nomor_kartu
                    ]);
            }

            DB::table('t_kasir')
                ->where('id', $req->id)
                ->update([
                    'metode_pembayaran' => $req->metode_pembayaran,
                    'nama_bank' => ($req->metode_pembayaran == 'TUNAI') ? null : $req->nama_bank,
                    'nomor_kartu' => ($req->metode_pembayaran == 'TUNAI') ? null : $req->nomor_kartu,
                    'nomor_transaksi' => ($req->metode_pembayaran == 'TUNAI') ? null : $req->nomor_transaksi
                ]);
                
            return Response()->json(['status' => 1, 'message' => 'Berhasil menyimpan data']);
        });
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model
                ->kasir()
                ->where('id', $req->id)
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

        $data = $this->model
            ->kasir()
            ->where('id', $req->id)
            ->first();
        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function getHistorisPembayaran(Request $req)
    {
        $data = $this->model
            ->KasirPembayaran()
            ->where('kasir_id', $req->id)
            ->get();

        return view('transaksi/cicilan/historis_pembayaran', compact('data'));
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model
                ->kasir()
                ->find($req->id)
                ->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function rekapInvoiceExcel(Request $req)
    {
        return Excel::download(new RekapInvoiceExport($req), 'rekap_invoice_' . CarbonParse(now(), 'd-M-Y') . '.xlsx');
    }

    public function uploadBuktiTransfer(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $kasir = $this->model->kasir()->find($req->id_bukti);

            // $nominalTransfer = convertNumber($req->nominal_transfer_bukti);
            // $countNominalTransfer = $kasir->nominal_transfer + $nominalTransfer;
            // $sisaPelunasan = convertNumber($req->sisa_pelunasan_bukti);
            // $countSisaPelunasan = $sisaPelunasan - $nominalTransfer;

            // var_dump($sisaPelunasan);
            // exit;

            $file = $req->file('bukti_transfer');

            if ($file != null) {
                $path = 'image/bukti_transfer_invoice';
                $id = Str::uuid($req->id_bukti)->toString();
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
                ->kasir()
                ->find($req->id_bukti)
                ->update([
                    // 'sisa_pelunasan' => $countSisaPelunasan,
                    'bukti_transfer' => $bukti_transfer,
                    'updated_at' => now(),
                    // 'nominal_transfer' => $countNominalTransfer,
                ]);

            return Response()->json(['status' => 1, 'message' => 'Berhasil menyimpan data']);
        });
    }
}

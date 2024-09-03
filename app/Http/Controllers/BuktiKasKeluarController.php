<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Image;

class BuktiKasKeluarController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('transaksi/bukti_kas_keluar/bukti_kas_keluar');
    }

    public function create(Request $req)
    {
        Auth::user()->akses('create', null, true);
        return view('transaksi/bukti_kas_keluar/create_bukti_kas_keluar');
    }

    public function aksi($data)
    {
        $edit = '';
        $tarik = '';
        $delete = '';
        if (Auth::user()->akses('edit')) {
            $edit = '<li>' .
                '<a href="' . route('editBuktiKasKeluar', ['id' => Crypt::encrypt($data->id)]) . '" class="dropdown-item text-info">' .
                '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah' .
                '</a>' .
                '</li>';
        }

        if (Auth::user()->akses('delete')) {
            $delete =  '<li>' .
                '<a href="javascript:;" onclick="hapus(\'' . $data->id . '\')" class="dropdown-item text-danger">' .
                '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus' .
                '</a>' .
                '</li>';
        }


        $print = '<li>' .
            '<a href="' . route('printBuktiKasKeluar', ['id' => Crypt::encrypt($data->id)]) . '" class="dropdown-item text-warning">' .
            '<i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Cetak' .
            '</a>' .
            '</li>';


        $lihatDetail = '<li>' .
            '<a href="javascript:;" onclick="lihatDetail(\'' . $data->id . '\')" class="dropdown-item text-success">' .
            '<i class="fa-solid fa-book-open"></i>&nbsp;&nbsp;&nbsp;Lihat Detail' .
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
            $delete .
            $print .
            $lihatDetail .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model->jurnal()
            ->where(function ($q) use ($req) {

                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if ($req->status != '') {
                    $q->where('status', $req->status);
                }

                if ($req->tanggal_awal != '') {
                    $q->where('tanggal', '>=', $req->tanggal_awal);
                }

                if ($req->tanggal_akhir != '') {
                    $q->where('tanggal', '<=', $req->tanggal_akhir);
                }

                $q->where('jenis', 'BUKTI KAS KELUAR');
            })
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            ->addColumn('branch', function ($data) {
                // return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
                return $data->Branch != null ? $data->Branch->lokasi  : "-";
            })
            ->addColumn('supplier', function ($data) {
                return $data->Supplier != null ? $data->Supplier->name  : "-";
            })
            ->addColumn('jumlah_item', function ($data) {
                return $data->JurnalDetail->count();
            })
            ->addColumn('nominal', function ($data) {
                return 'Rp. ' . ' ' . number_format($data->nominal);
            })
            ->addColumn('tanggal', function ($data) {
                return CarbonParse($data->tanggal, 'd-M-Y');
            })
            ->addColumn('status', function ($data) {
                if ($data->status == 'Released') {
                    return '<span class="badge badge-warning">Belum Disetujui</span>';
                }

                if ($data->status == 'Approved') {
                    return '<span class="badge badge-primary">Approved</span>';
                }

                if ($data->status == 'Rejected') {
                    return '<span class="badge badge-danger">Rejected</span>';
                }
            })

            ->addColumn('updated_by', function ($data) {
                return $data->UpdatedBy ? $data->UpdatedBy->name : '-';
            })

            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'status', 'tanggal', 'updated_by'])
            ->addIndexColumn()
            ->make(true);
    }

    public function datatableDetail(Request $req)
    {
        $data = $this->model->jurnalDetail()
            ->whereHas('jurnal', function ($q) use ($req) {
                $q->where('id', $req->id);
            })
            ->get();


        return DataTables::of($data)
            ->addColumn('proofment', function ($data) {
                if ($data->proofment != null) {
                    return '<a href="' . url('/') . '/' . $data->proofment . '" download="' . $data->proofment . '"><img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->proofment . '" alt="No image"></a>';
                } else {
                    return "-";
                }
            })
            ->addColumn('master_akun_transaksi', function ($data) {
                return $data->masterAkunTransaksi->name;
            })

            ->addColumn('nominal', function ($data) {
                return 'Rp. ' . ' ' . number_format($data->sub_total);
            })
            ->rawColumns(['proofment'])
            ->addIndexColumn()
            ->make(true);
    }


    public function generateKode(Request $req)
    {
        $branch = $this->model->branch()->find($req->branch_id);
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'BKK-' . $branch->kode . '-' . $tanggal . '-';
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

    public function generateKodePenerimaan(Request $req)
    {
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'IS-' . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->penerimaanStock()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->penerimaanStock()
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
                DB::statement('LOCK TABLE t_jurnal, t_jurnal_detail IN SHARE MODE');
                // DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                Auth::user()->akses('create', null, true);
                // $id = $this->model->jurnal()->max('id') + 1;
                $kode = $this->generateKode($req)->getData()->kode;
                $jurnal = $this->model->jurnal()
                    ->create([
                        // 'id'    => $id,
                        'kode'  => $kode,
                        'branch_id' => $req->branch_id,
                        'tanggal'   => dateStore($req->tanggal),
                        'ref'   => $kode,
                        'jenis'   => 'BUKTI KAS KELUAR',
                        'metode_pembayaran'   => $req->metode_pembayaran,
                        'nama_bank'   => $req->nama_bank,
                        'nomor_kartu'   => $req->nomor_kartu,
                        'dk'    => 'KREDIT',
                        'description'    => strtoupper($req->keterangan),
                        'nominal'   => convertNumber($req->total, true),
                        'status'    => 'Approved',
                        'created_by'    => me(),
                        'updated_by'    => me(),
                                ]);
                
                $id = $jurnal->id;

                foreach ($req->master_akun_transaksi_id as $i => $d) {

                    $file = $req->file('proofment_' . $i);
                    if ($file != null) {
                        $path = 'image/bukti_kas_keluar';
                        $data =  Str::uuid('Bukti Kas Keluar' . $id . $i)->toString();
                        $name = $data . '.' . $file->getClientOriginalExtension();
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

                        $fileName = $foto;
                    } else {
                        $fileName = null;
                    }

                    $this->model->jurnalDetail()
                        ->create([
                            'jurnal_id' => $id,
                            'id'    => $i + 1,
                            'master_akun_transaksi_id'  => $d,
                            'redaksi'   => strtoupper($req->redaksi[$i]),
                            'harga' => convertNumber($req->harga[$i]),
                            'qty'   => 1,
                            'proofment'   => $fileName,
                            'sub_total' => convertNumber($req->harga[$i]),
                        ]);
                }

                DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan', 'kode' => $kode]);
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }
        });
    }

    public function pindahStock($jenisStock, $column, $itemId, $branchId, $qty, $hargaSatuan, $totalHarga, $kode)
    {
        $checkStock = $this->model->stock()
            ->where('jenis_stock', $jenisStock)
            ->where($column, $itemId)
            ->where('branch_id', $branchId)
            ->first();

        if (is_null($checkStock)) {
            $idStock = $this->model->stock()
                ->max('id') + 1;

            $this->model->stock()
                ->create([
                    'id'    => $idStock,
                    'jenis_stock'   => $jenisStock,
                    'branch_id' => $branchId,
                    'produk_obat_id'    => $jenisStock == 'OBAT' ? $itemId : null,
                    'item_non_obat_id'  => $jenisStock == 'NON OBAT' ? $itemId : null,
                    'qty'   => convertNumber($qty),
                    'created_by'    => me(),
                    'updated_by'    => me(),
                ]);
        } else {
            $idStock = $checkStock->id;
            $this->model->stock()
                ->find($checkStock->id)
                ->update([
                    'qty'   => $checkStock->qty + convertNumber($qty),
                    'updated_by'    => me(),
                ]);
        }

        // $idMutasiStock = $this->model->mutasiStock()
        //     ->max('id') + 1;

        try {
            DB::beginTransaction();
            $this->model->mutasiStock()
                ->create([
                    'stock_id'  => $idStock,
                    // 'id'    => $idMutasiStock,
                    'harga_satuan'  => convertNumber($hargaSatuan),
                    'total_harga'   => convertNumber($totalHarga),
                    'qty'   => convertNumber($qty),
                    'qty_tersisa'   => convertNumber($qty),
                    'referensi' => $kode,
                    'jenis' => 'PENERIMAAN',
                    'created_by'    => me(),
                    'updated_by'    => me(),
                ]);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
        }
    }

    public function update(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('edit', null, true);
            $id = $req->id;

            $kode = $req->kode;
            $this->model->jurnal()
                ->find($id)
                ->update([
                    'id'    => $id,
                    'kode'  => $kode,
                    'branch_id' => $req->branch_id,
                    'tanggal'   => dateStore($req->tanggal),
                    'ref'   => $kode,
                    'jenis'   => 'BUKTI KAS KELUAR',
                    'dk'    => 'KREDIT',
                    'metode_pembayaran' => $req->metode_pembayaran,
                    'nama_bank' => $req->nama_bank,
                    'nomor_kartu' => $req->nomor_kartu,
                    'description'    => strtoupper($req->keterangan),
                    'nominal'   => convertNumber($req->total, true),
                    'updated_by'    => me(),
                    'approved_by'    => isset($req->status) ? me() : null,
                ]);


            foreach ($req->master_akun_transaksi_id as $i => $d) {

                if ($req->jurnal_id[$i] != '') {
                    $jurnal = $this->model->jurnalDetail()
                        ->where('jurnal_id', $id)
                        ->where('id', $req->jurnal_id[$i])
                        ->first();
                } else {
                    $jurnal = null;
                }

                if ($jurnal) {
                    $file = $req->file('proofment_' . $i);
                    if ($file != null) {
                        $path = 'image/bukti_kas_keluar';
                        $data =  Str::uuid('Bukti Kas Keluar' . $id . $req->jurnal_id[$i])->toString();
                        $name = $data . '.' . $file->getClientOriginalExtension();
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

                        $fileName = $foto;
                    } else {
                        $fileName = $jurnal->proofment;
                    }


                    $this->model->jurnalDetail()
                        ->where('jurnal_id', $id)
                        ->where('id', $req->jurnal_id[$i])
                        ->update([
                            'master_akun_transaksi_id'  => $d,
                            'redaksi'   => strtoupper($req->redaksi[$i]),
                            'harga' => convertNumber($req->harga[$i]),
                            'proofment'   => $fileName,
                            'qty'   => 1,
                            'sub_total' => convertNumber($req->harga[$i]),
                        ]);
                } else {
                    $file = $req->file('proofment_' . $i);

                    $jurnal = $this->model->jurnalDetail()
                        ->where('jurnal_id', $id)
                        ->max('id') + 1;
                    if ($file != null) {
                        $path = 'image/bukti_kas_keluar';
                        $data =  Str::uuid('Bukti Kas Keluar' . $id . $jurnal)->toString();
                        $name = $data . '.' . $file->getClientOriginalExtension();
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

                        $fileName = $foto;
                    } else {
                        $fileName = null;
                    }

                    $this->model->jurnalDetail()
                        ->create([
                            'jurnal_id' => $id,
                            'id'    => $jurnal,
                            'master_akun_transaksi_id'  => $d,
                            'redaksi'   => strtoupper($req->redaksi[$i]),
                            'harga' => convertNumber($req->harga[$i]),
                            'qty'   => 1,
                            'proofment'   => $fileName,
                            'sub_total' => convertNumber($req->harga[$i]),
                        ]);
                }
            }

            return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah', 'kode' => $req->kode]);
        });
    }

    public function terima($id)
    {
        $id = Crypt::decrypt($id);
        Auth::user()->akses('validation', null, true);
        $ps = $this->model->jurnal()
            ->findOrFail($id);
        $data = $this->model->jurnal()
            ->findOrFail($id);
        return view('transaksi/bukti_kas_keluar/terima_bukti_kas_keluar', compact('data'));
    }

    public function status(Request $req)
    {
        Auth::user()->akses('validation', null, true);
        return DB::transaction(function () use ($req) {
            $this->model->jurnal()->find($req->id)
                ->update([
                    'status' => 'Rejected',
                    'alasan' => $req->alasan,
                    'approved_by' => me(),
                ]);
            return Response()->json(['status' => 1, 'message' => 'Berhasil menolak bukti kas keluar']);
        });
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        Auth::user()->akses('edit', null, true);
        $ps = $this->model->jurnal()
            ->findOrFail($id);
        $data = $this->model->jurnal()
            ->findOrFail($id);
        return view('transaksi/bukti_kas_keluar/edit_bukti_kas_keluar', compact('data'));
    }


    public function print($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('print', null, true);
        $ps = $this->model->jurnal()
            ->findOrFail($id);
        $data = $this->model->jurnal()
            ->findOrFail($id);

        $pdf = PDF::loadview('transaksi/bukti_kas_keluar/print_bukti_kas_keluar', compact('data'))->setPaper('a4', 'potrait');
        return $pdf->stream('Nota Pengeluaran Stock-' . $data->kode . '-' . carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function lihat($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('edit', null, true);
        $ps = $this->model->jurnal()
            ->findOrFail($id);
        $data = $this->model->jurnal()
            ->findOrFail($id);
        return view('transaksi/bukti_kas_keluar/lihat_bukti_kas_keluar', compact('data'));
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);

            $this->model->jurnal()->find($req->id)->delete();

            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function select2(Request $req)
    {

        $id = isset($req->id) ? $req->id : 0;

        switch ($req->param) {
            case 'supplier_id':
                return $this->model->supplier()
                    ->select('id', DB::raw("name as text"), 'ms_supplier.*')
                    ->where('status', true)
                    ->where(function ($q) use ($req) {
                        $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                    })
                    ->paginate(10);
            case 'item_id':
                if ($req->jenis_item == 'OBAT') {
                    return $this->model->produkObat()
                        ->select('id', DB::raw("name as text"), 'mo_produk_obat.*')
                        ->with(['Satuan'])
                        ->withCount(['MutasiStock as stock' => function ($q) use ($req) {
                            $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                            $q->where('jenis', 'PENERIMAAN');
                            $q->where('branch_id', $req->branch_id);
                        }])
                        ->withCount(['PengeluaranStockDetail as reverse' => function ($q) use ($id) {
                            $q->select(DB::raw('coalesce(sum(qty),0)'));
                            $q->where('pengeluaran_stock_id', $id);
                        }])
                        ->where('status', true)
                        ->where(function ($q) use ($req) {
                            $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                        })
                        ->paginate(10);
                } elseif ($req->jenis_item == 'NON OBAT') {
                    return $this->model->itemNonObat()
                        ->select('id', DB::raw("name as text"), 'ms_item_non_obat.*')
                        ->with(['Satuan'])
                        ->withCount(['MutasiStock as stock' => function ($q) use ($req) {
                            $q->select(DB::raw('coalesce(sum(qty_tersisa),0)'));
                            $q->where('jenis', 'PENERIMAAN');
                            $q->where('branch_id', $req->branch_id);
                        }])
                        ->withCount(['PengeluaranStockDetail as reverse' => function ($q) use ($id) {
                            $q->select(DB::raw('coalesce(sum(qty),0)'));
                            $q->where('pengeluaran_stock_id', $id);
                        }])

                        ->where('status', true)
                        ->where(function ($q) use ($req) {
                            $q->where(DB::raw("UPPER(CONCAT(name))"), 'like', '%' . strtoupper($req->q) . '%');
                        })
                        ->paginate(10);
                }

            default:
                # code...
                break;
        }
    }
}

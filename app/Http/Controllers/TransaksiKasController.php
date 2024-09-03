<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Database\QueryException;

class TransaksiKasController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('transaksi/transaksi_kas/transaksi_kas');
    }

    public function create(Request $req)
    {
        Auth::user()->akses('create', null, true);
        return view('transaksi/transaksi_kas/create_transaksi_kas');
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

                if ($req->dk != '') {
                    $q->where('dk', $req->dk);
                }

                $q->where('jenis', 'TRANSAKSI KAS');
            })
            ->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return view('transaksi/transaksi_kas/action_button_transaksi_kas', compact('data'));
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->kode . ' ' . $data->Branch->lokasi  : "-";
            })
            ->addColumn('supplier', function ($data) {
                return $data->Supplier != null ? $data->Supplier->name  : "-";
            })
            ->addColumn('jumlah_item', function ($data) {
                return $data->JurnalDetail->count();
            })
            ->addColumn('nominal', function ($data) {
                return number_format($data->nominal);
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

            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'status'])
            ->addIndexColumn()
            ->make(true);
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


    public function generateKode(Request $req)
    {
        $branch = $this->model->branch()->find($req->branch_id);
        $tanggal = Carbon::now()->format('Ym');
        $kode = 'TK-' . $branch->kode . '-' . $tanggal . '-';
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

                Auth::user()->akses('create', null, true);
                // $id = $this->model->jurnal()->max('id') + 1;

                $kode = $this->generateKode($req)->getData()->kode;

                $file = $req->file('attachment');
                if ($file != null) {
                    $path = 'image/transaksi_kas_cabang';
                    $uuid =  Str::uuid($id)->toString();
                    $name = $uuid . '.' . $file->getClientOriginalExtension();
                    $attachment = $path . '/' . $name;
                    if (is_file($attachment)) {
                        unlink($attachment);
                    }

                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }

                    Storage::disk('public_uploads')->put($attachment, file_get_contents($file));
                } else {
                    $attachment = null;
                }

                $jurnal = $this->model->jurnal()
                    ->create([
                        // 'id'    => $id,
                        'kode'  => $kode,
                        'branch_id' => $req->branch_id,
                        'tanggal'   => dateStore($req->tanggal),
                        'ref'   => $kode,
                        'jenis'   => 'TRANSAKSI KAS',
                        'dk'    => $req->dk,
                        'attachment'    => $attachment,
                        'description'    => strtoupper($req->keterangan),
                        'nominal'   => convertNumber($req->total),
                        'status'    => $req->dk == 'DEBET' ? 'Approved' : 'Released',
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);
                
                $id = $jurnal->id;

                foreach ($req->master_akun_transaksi_id as $i => $d) {
                    $this->model->jurnalDetail()
                        ->create([
                            'jurnal_id' => $id,
                            'id'    => $i + 1,
                            'master_akun_transaksi_id'  => $d,
                            'redaksi'   => strtoupper($req->redaksi[$i]),
                            'harga' => convertNumber($req->harga[$i]),
                            'qty'   => 1,
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
        try {
            DB::beginTransaction();
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

            $data = $this->model->jurnal()->find($req->id);

            $file = $req->file('attachment');
            if ($file != null) {
                $path = 'image/transaksi_kas';
                $uuid =  Str::uuid($id)->toString();
                $name = $uuid . '.' . $file->getClientOriginalExtension();
                $attachment = $path . '/' . $name;
                if (is_file($attachment)) {
                    unlink($attachment);
                }

                if (!file_exists($path)) {
                    $oldmask = umask(0);
                    mkdir($path, 0777, true);
                    umask($oldmask);
                }

                Storage::disk('public_uploads')->put($attachment, file_get_contents($file));
            } else {
                $attachment = $data->attachment;
            }

            $this->model->jurnal()
                ->find($id)
                ->update([
                    'id'    => $id,
                    'kode'  => $kode,
                    'branch_id' => $req->branch_id,
                    'tanggal'   => dateStore($req->tanggal),
                    'ref'   => $kode,
                    'jenis'   => 'TRANSAKSI KAS',
                    'dk'    => $req->dk,
                    'attachment'    => $attachment,
                    'description'    => strtoupper($req->keterangan),
                    'nominal'   => convertNumber($req->total),
                    'updated_by'    => me(),
                    'status'    => isset($req->status) ? $req->status : ($req->dk == 'DEBET' ? 'Approved' : 'Released'),
                    'approved_by'    => isset($req->status) ? me() : null,
                ]);

            $this->model->jurnalDetail()->where('jurnal_id', $id)->delete();
            foreach ($req->master_akun_transaksi_id as $i => $d) {
                $this->model->jurnalDetail()
                    ->create([
                        'jurnal_id' => $id,
                        'id'    => $i + 1,
                        'master_akun_transaksi_id'  => $d,
                        'redaksi'   => strtoupper($req->redaksi[$i]),
                        'harga' => convertNumber($req->harga[$i]),
                        'qty'   => 1,
                        'sub_total' => convertNumber($req->harga[$i]),
                    ]);
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
        return view('transaksi/transaksi_kas/terima_transaksi_kas', compact('data'));
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
            return Response()->json(['status' => 1, 'message' => 'Berhasil menolak transaksi kas']);
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
        return view('transaksi/transaksi_kas/edit_transaksi_kas', compact('data'));
    }


    public function print($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('print', null, true);
        $ps = $this->model->jurnal()
            ->findOrFail($id);
        $data = $this->model->jurnal()
            ->findOrFail($id);

        $pdf = PDF::loadview('transaksi/transaksi_kas/print_transaksi_kas', compact('data'))->setPaper('a4', 'potrait');
        return $pdf->stream('Nota Pengeluaran Stok-' . $data->kode . '-' . carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function lihat($id)
    {
        $id = crypt::decrypt($id);
        Auth::user()->akses('edit', null, true);
        $ps = $this->model->jurnal()
            ->findOrFail($id);
        $data = $this->model->jurnal()
            ->findOrFail($id);
        return view('transaksi/transaksi_kas/lihat_transaksi_kas', compact('data'));
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $data = $this->model->jurnal()->find($req->id);
            $allowDelete = true;
            if ($data->jenis == 'PINDAH CABANG') {
                if ($data->PenerimaanStock->status != 'Belum Diterima') {
                    $allowDelete = false;
                }
            }

            if ($allowDelete) {
                $this->revertStock($req);
                $this->model->jurnal()->find($req->id)->delete();

                $this->model->penerimaanStock()
                    ->where('pengeluaran_stock_id', $req->id)
                    ->delete();
                return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
            } else {
                return Response()->json(['status' => 2, 'message' => 'Branch tujuan telah menerima item ini.']);
            }
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

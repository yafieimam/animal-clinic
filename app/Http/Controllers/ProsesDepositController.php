<?php

namespace App\Http\Controllers;

use App\Models\Modeler;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Image;

class ProsesDepositController extends Controller
{
    public $model;
    public $notify;
    public function __construct()
    {
        $this->model  = new Modeler();
        $this->notify  = new NotifyController();
    }

    public function index(Request $req)
    {
        if (isset($req->notification_id)) {
            Auth::user()->unreadNotifications->where('id', $req->notification_id)->markAsRead();
        }
        Auth::user()->akses('view', null, true);
        return view('transaksi/proses_deposit/proses_deposit');
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        $cancel = '';
        $edit = '<li>' .
            '<a href="javascript:;" onclick="edit(\'' . $data->deposit_id . '\',\'' . $data->id . '\')" class="dropdown-item text-info">' .
            '<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;Selesaikan Transfer' .
            '</a>' .
            '</li>';
        if($data->metode_pembayaran == 'TRANSFER' || $data->metode_pembayaran == 'DEBET'){
            $cancel = '<li>' .
                '<a href="javascript:;" onclick="cancel(\'' . $data->deposit_id . '\',\'' . $data->id . '\')" class="dropdown-item text-danger">' .
                '<i class="fa fa-times"></i>&nbsp;&nbsp;&nbsp;Batalkan' .
                '</a>' .
                '</li>';
        }

        // if (Auth::user()->akses('delete')) {
        //     $delete =  '<li>' .
        //         '<a href="javascript:;" onclick="hapus(\'' . $data->id . '\')" class="dropdown-item text-danger">' .
        //         '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus' .
        //         '</a>' .
        //         '</li>';
        // }


        return '<div class="dropdown">' .
            '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
            '<span class="w-5 h-5 flex items-center justify-center">' .
            '<i class="fa fa-bars"></i>' .
            '</span>' .
            '</button>' .
            '<div class="dropdown-menu w-52 ">' .
            '<ul class="dropdown-content">' .
            $edit . $cancel .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model->deposit_mutasi()
            ->whereNotNull('status')
            // ->where('metode_pembayaran', '!=', 'TUNAI')
            ->where(function ($q) use ($req) {
                if ($req->status != '') {
                    $q->where('status', $req->status);
                }

                if ($req->tanggal_awal != '') {
                    $q->whereDate('created_at', '>=', $req->tanggal_awal);
                }else{
                    $q->whereDate('created_at', '>=', date('Y-m-d'));
                }

                if ($req->tanggal_akhir != '') {
                    $q->whereDate('created_at', '<=', $req->tanggal_akhir);
                }else{
                    $q->whereDate('created_at', '<=', date('Y-m-d'));
                }
            })
            ->whereHas('deposit', function ($q) use ($req) {
                if ($req->owner_id != '') {
                    $q->where('owner_id', $req->owner_id);
                }

                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
                }
            })
            ->orderBy('created_at', 'DESC')
            // ->take(10)
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                if ($data->status == 'Released') {
                    return $this->aksi($data);
                } else if ($data->status == 'Cancel') {
                    return '<span class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">' . $data->status . '</span>';
                }else {
                    return '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">' . $data->status . '</span>';
                }
            })
            ->addColumn('status', function ($data) {
                switch ($data->status) {
                    case 'Released':
                        return '<span class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">' . $data->status . '</span>';
                        break;
                    case 'Done':
                        return '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">' . $data->status . '</span>';
                        break;
                    case 'Cancel':
                        return '<span class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">' . $data->status . '</span>';
                        break;
                    default:
                        # code...
                        break;
                }
            })
            ->addColumn('icon', function ($data) {
                return '<i class="' . $data->icon . ' text-2xl"></i>';
            })
            ->addColumn('nilai', function ($data) {
                return 'Rp. ' . ' ' . number_format($data->nilai);
            })
            ->addColumn('owner', function ($data) {
                return $data->Deposit->owner ? $data->Deposit->owner->name : '';
            })
            ->addColumn('kode', function ($data) {
                return $data->Deposit->owner ? $data->Deposit->owner->kode : '';
            })
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy ? $data->CreatedBy->name : '-';
            })
            ->addColumn('updated_by', function ($data) {
                if ($data->status != 'Released') {
                    return $data->UpdatedBy ? $data->UpdatedBy->name : '-';
                } else {
                    return "-";
                }
            })
            ->addColumn('created_at', function ($data) {
                return date('d-M-Y', strtotime($data->created_at));
            })
            ->addColumn('updated_at', function ($data) {
                if ($data->status != 'Released') {
                    return date('d-M-Y', strtotime($data->updated_at));
                } else {
                    return "-";
                }
            })
            ->addColumn('bukti_transfer', function ($data) {
                if ($data->bukti_transfer != null) {
                    return '<a href="' . url('/') . '/' . $data->bukti_transfer . '" target="_blank"><img style="width:100px;height:100px;object-fit:cover;cursor:pointer" src="' . url('/') . '/' . $data->bukti_transfer . '" alt="No image"></a>';
                } else {
                    return "-";
                }
            })
            ->addColumn('nama_bank', function ($data) {
                if ($data->nama_bank != null) {
                    return $data->nama_bank ? $data->nama_bank : '-';
                } else {
                    return "-";
                }
            })
            ->addColumn('nomor_kartu', function ($data) {
                if ($data->nomor_kartu != null) {
                    return $data->nomor_kartu ? $data->nomor_kartu : '-';
                } else {
                    return "-";
                }
            })
            ->addColumn('atas_nama', function ($data) {
                if ($data->atas_nama != null) {
                    return $data->atas_nama ? $data->atas_nama : '-';
                } else {
                    return "-";
                }
            })
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'bukti_transfer', 'created_at', 'kode', 'nama_bank', 'nomor_kartu', 'atas_nama'])
            ->addIndexColumn()
            ->make(true);
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('dmY');
        $kode = 'DEPO-'  . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->deposit()
            ->selectRaw('max(cast(substring(kode,' . $sub . ') as INTEGER )) as id')
            ->where('kode', 'like', $kode . '%')
            ->first();

        $collect = $this->model->deposit()
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
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                $check = $this->model->deposit_mutasi()
                    ->where('deposit_id', $req->deposit_id)
                    ->where('id', $req->id)
                    ->first();
                if ($check->status == 'Done') {
                    DB::rollBack();
                    return Response()->json(['status' => 2, 'message' => 'Deposit sudah di cairkan, tidak bisa mengulangi aksi yg sama.']);
                }
                // $idJurnal = $this->model->jurnal()->max('id') + 1;
                $kodeJurnal = generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode;
                // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);
                $this->model->jurnal()
                    ->create([
                        // 'id'    => $idJurnal,
                        'kode'  => $kodeJurnal,
                        'branch_id' =>  $check->CreatedBy->branch_id,
                        'tanggal'   => dateStore(),
                        'ref'   => $check->deposit->kode,
                        'ref_id'   => $req->id,
                        'metode_pembayaran' => $check->metode_pembayaran,
                        'jenis'   => 'DEPOSIT',
                        'dk'    => 'KREDIT',
                        'description'    => 'PENGELUARAN DEPOSIT ATAS KODE DEPOSIT ' . $check->deposit->kode,
                        'nominal'   => $check->nilai,
                        'created_by'    => me(),
                        'updated_by'    => me(),
                    ]);

                $file = $req->file('bukti_transfer');

                if ($file != null) {
                    $path = 'image/bukti_transfer_deposit';
                    $id =  Str::uuid($req->deposit_id . $req->id)->toString();
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
                } else {
                    $foto = null;
                }

                $check = $this->model->deposit_mutasi()
                    ->where('deposit_id', $req->deposit_id)
                    ->where('id', $req->id)
                    ->update([
                        'status' => 'Done',
                        // 'created_at' => now(),
                        'bukti_transfer' => $foto,
                        'updated_by' => me(),
                        // 'updated_at' => DB::raw('updated_at'),
                    ]);

                DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
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
            $this->model->deposit()->where('id', $req->id)
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

        $data = $this->model->deposit_mutasi()
            ->with(['deposit'])
            ->where('deposit_id', $req->deposit_id)
            ->where('id', $req->id)
            ->first();

        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        Auth::user()->akses('delete', null, true);
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);
            $this->model->deposit()->find($req->id)->delete();
            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function cancel(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $check = $this->model->deposit_mutasi()
                ->where('deposit_id', $req->deposit_id_cancel)
                ->where('id', $req->id_cancel)
                ->update([
                    'keterangan' => $req->keterangan_cancel,
                    'status' => 'Cancel',
                    'updated_by' => me(),
                ]);
            
            // var_dump(reCalcDeposit($req->deposit_id));
            // exit;

            $this->model->deposit()
                ->where('id', $req->deposit_id_cancel)
                ->update([
                    'nilai_deposit' => reCalcDeposit($req->deposit_id_cancel),
                    'sisa_deposit' => reCalcDeposit($req->deposit_id_cancel),
                    'updated_by'    => me(),
                ]);

            return Response()->json(['status' => 1, 'message' => 'Data berhasil dibatalkan']);
        });
    }


    public function print(Request $req)
    {
        Auth::user()->akses('print', null, true);
        $data = $this->model->deposit()->where('id', $req->id)->first();

        $pdf = Pdf::loadView('transaksi/deposit/print', compact('data'))
            ->setPaper('a4', 'potrait');
        return $pdf->stream();
    }


    public function update(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);

            $check = $this->model->deposit()
                ->where('id', $req->id)
                ->where('owner_id', $req->owner_id)
                ->first();

            $idDepositMutasi = $this->model->deposit_mutasi()->where('deposit_id', $check->id)->max('id') + 1;

            $this->model->deposit_mutasi()
                ->create([
                    'deposit_id'    => $check->id,
                    'id'    => $idDepositMutasi,
                    'jenis_deposit' => 'KREDIT',
                    'branch_id' => Auth::user()->branch_id,
                    'nilai' => convertNumber($req->pengurangan),
                    'keterangan'    => $req->keterangan,
                    'created_by'    => me(),
                    'updated_by'    => me(),
                ]);

            $this->model->deposit()
                ->where('id', $check->id)
                ->update([
                    'nilai_deposit' => reCalcDeposit($check->id),
                    'sisa_deposit' => reCalcDeposit($check->id),
                    'updated_by'    => me(),
                ]);

            return Response()->json(['status' => 1, 'message' => 'Berhasil mengurangi deposit']);
        });
    }
    public function tarik(Request $req)
    {
        return DB::transaction(function () use ($req) {
            try {
                DB::beginTransaction();
                // DB::statement('LOCK TABLE t_jurnal, t_deposit_mutasi IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                $input = $req->all();

                unset($input['_token']);

                $check = $this->model->deposit()
                    ->where('id', $req->id)
                    ->where('owner_id', $req->owner_id)
                    ->first();

                $idDepositMutasi = $this->model->deposit_mutasi()->where('deposit_id', $check->id)->max('id') + 1;

                if ($req->jenis_pembayaran == 'TUNAI') {
                    // $idJurnal = $this->model->jurnal()->max('id') + 1;
                    $kodeJurnal = generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode;
                    // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);
                    $idDepositMutasi = $this->model->deposit_mutasi()->where('deposit_id', $check->id)->max('id') + 1;

                    $this->model->deposit_mutasi()
                        ->create([
                            'deposit_id'    => $check->id,
                            'id'    => $idDepositMutasi,
                            'branch_id' => Auth::user()->branch_id,
                            'jenis_deposit' => 'KREDIT',
                            'nilai' => convertNumber($req->tarik),
                            'keterangan'    => 'PENGAMBILAN DEPOSIT',
                            'keterangan2'    => $req->keterangan2,
                            'metode_pembayaran' => $req->metode_pembayaran,
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'status'    => 'Released',
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    $this->model->jurnal()
                        ->create([
                            // 'id'    => $idJurnal,
                            'kode'  => $kodeJurnal,
                            'branch_id' =>  Auth::user()->Branch->id,
                            'tanggal'   => dateStore(),
                            'ref'   => $check->kode,
                            'jenis'   => 'KASIR',
                            'dk'    => 'KREDIT',
                            'description'    => 'PENGELUARAN DEPOSIT ATAS KODE DEPOSIT ' . $check->kode,
                            'nominal'   => convertNumber($req->tarik),
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                } else {
                    $this->model->deposit_mutasi()
                        ->create([
                            'deposit_id'    => $check->id,
                            'id'    => $idDepositMutasi,
                            'branch_id' => Auth::user()->branch_id,
                            'jenis_deposit' => 'KREDIT',
                            'nilai' => convertNumber($req->tarik),
                            'metode_pembayaran' => $req->metode_pembayaran,
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'keterangan'    => $req->keterangan,
                            'status'    => 'Released',
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    $this->notify->notifyDeposit($check->id);
                }

                $this->model->deposit()
                    ->where('id', $check->id)
                    ->update([
                        'nilai_deposit' => reCalcDeposit($check->id),
                        'sisa_deposit' => reCalcDeposit($check->id),
                        'updated_by'    => me(),
                    ]);

                DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Berhasil mengurangi deposit']);
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Modeler;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;

class DepositController extends Controller
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
        // selarasCabangDeposit();
        Auth::user()->akses('view', null, true);
        return view('transaksi/deposit/deposit');
    }

    public function aksi($data)
    {
        $edit = '';
        $tarik = '';
        $delete = '';
        // if (Auth::user()->akses('edit')) {
        //     $edit = '<li>' .
        //         '<a href="javascript:;" onclick="edit(\'' . $data->id . '\')" class="dropdown-item text-danger">' .
        //         '<i class="fa fa-minus"></i>&nbsp;&nbsp;&nbsp;Kurangi Nominal' .
        //         '</a>' .
        //         '</li>';
        // }

        // if (Auth::user()->akses('delete')) {
        //     $delete =  '<li>' .
        //         '<a href="javascript:;" onclick="hapus(\'' . $data->id . '\')" class="dropdown-item text-danger">' .
        //         '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus' .
        //         '</a>' .
        //         '</li>';
        // }

        $tarik = '<li>' .
            '<a href="javascript:;" onclick="tarik(\'' . $data->id . '\')" class="dropdown-item text-info">' .
            '<i class="fa fa-dollar"></i>&nbsp;&nbsp;&nbsp;Penarikan Deposit' .
            '</a>' .
            '</li>';

        $print = '<li>' .
            '<a href="javascript:;" onclick="printDeposit(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
            '<i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Cetak Bukti Deposit' .
            '</a>' .
            '</li>';

        $historyPemakaian = '<li>' .
            '<a href="javascript:;" onclick="historiPemakaian(\'' . $data->id . '\')" class="dropdown-item text-success">' .
            '<i class="fa-solid fa-book-open"></i>&nbsp;&nbsp;&nbsp;Lihat Histori' .
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
            $tarik .
            $delete .
            $print .
            $historyPemakaian .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatableHistoriPemakaian(Request $req)
    {
        $data = $this->model->deposit_mutasi()
            ->whereHas('deposit', function ($q) use ($req) {
                $q->where('id', $req->id);
            })
            ->get();

        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return '<button class="btn btn-warning" type="button"  onclick="printHistoryDeposit(\'' . $data->deposit_id . '\',\'' . $data->id . '\')"><i class="fas fa-print"></i></button>';
            })
            ->addColumn('status', function ($data) {
                switch ($data->status) {
                    case 'Released':
                        return '<span class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">' . $data->status . '</span>';
                        break;
                    case 'Done':
                        return '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">' . $data->status . '</span>';
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
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy ? $data->CreatedBy->name : '-';
            })
            ->addColumn('updated_by', function ($data) {
                return $data->UpdatedBy ? $data->UpdatedBy->name : '-';
            })
            ->addColumn('kode', function ($data) {
                return $data->kode ? $data->kode : '-';
            })
            ->addColumn('ref', function ($data) {
                if ($data->jenis_deposit == 'DEBET') {
                    return $data->Deposit->kode ? $data->Deposit->kode : '-';
                } else {
                    return $data->ref ? $data->ref : '-';
                }
            })
            ->addColumn('created_at', function ($data) {
                return CarbonParse($data->created_at, 'd-M-Y');
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
            ->addColumn('bukti_transfer', function ($data) {
                if ($data->bukti_transfer != null) {
                    return '<a href="' . url('/') . '/' . $data->bukti_transfer . '" target="_blank"><img style="width:100px;height:100px;object-fit:cover" src="' . url('/') . '/' . $data->bukti_transfer . '" alt="No image"></a>';
                } else {
                    return "-";
                }
            })
            ->addColumn('jenis_deposit', function ($data) {
                if ($data->jenis_deposit == 'DEBET') {
                    return '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">UANG MASUK</span>';
                } else {
                    return '<span class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">UANG KELUAR</span>';
                }
            })
            ->addColumn('status_deposit', function ($data) {
                if ($data->status == 'Cancel') {
                    return '<span class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Batal</span>';
                } elseif ($data->status == 'Released') {
                    return '<span class="bpy-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Sedang Proses</span>';
                } else {
                    return '<span class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Berhasil</span>';
                }
            })
            ->addColumn('keterangan2', function ($data) {
                if ($data->keterangan2 != null) {
                    return $data->keterangan2 ? $data->keterangan2 : '-';
                } else {
                    return "-";
                }
            })
            
            ->rawColumns(['aksi', 'status', 'icon', 'sequence', 'bukti_transfer', 'created_by', 'updated_by', 'jenis_deposit', 'kode', 'ref', 'nama_bank', 'nomor_kartu', 'status_deposit', 'keterangan2'])
            ->addIndexColumn()
            ->make(true);
    }


    public function datatable(Request $req)
    {
        // $data = $this->model->deposit_mutasi()
        //     ->where('keterangan', '!=', 'PEMBAYARAN MENGGUNAKAN DEPOSIT')
        //     ->whereHas('deposit', function ($q) use ($req) {
        //         if ($req->owner_id != '') {
        //             $q->where('owner_id', $req->owner_id);
        //         }

        //         if ($req->branch_id != '') {
        //             $q->where('branch_id', $req->branch_id);
        //         }

        //         if (!Auth::user()->akses('global')) {
        //             $q->where('branch_id', Auth::user()->branch_id);
        //         }
        //     })
        //     ->where(function ($q) use ($req) {
        //         if ($req->tanggal_awal != '') {
        //             $q->whereDate('updated_at', '>=', $req->tanggal_awal);
        //         }else{
        //             $q->whereDate('updated_at', '>=', date('Y-m-d'));
        //         }

        //         if ($req->tanggal_akhir != '') {
        //             $q->whereDate('updated_at', '<=', $req->tanggal_akhir);
        //         }else{
        //             $q->whereDate('updated_at', '<=', date('Y-m-d'));
        //         }
        //     })->get();

        $data = $this->model->deposit()
            ->with(['Branch', 'DepositMutasi', 'Owner', 'CreatedBy', 'UpdatedBy'])
            ->withCount([
                'depositMutasi as total_uang_masuk' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nilai),0)"));
                    $q->where('jenis_deposit', 'DEBET');
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
                    $q->where('keterangan', 'NOT LIKE', '%PEMBAYARAN MENGGUNAKAN DEPOSIT%');
                    $q->where('keterangan', 'NOT LIKE', '%PENGAMBILAN DEPOSIT%');
                    $q->where('keterangan', 'NOT LIKE', '%PENGAMBILAN SISA DEPOSIT%');
                },
                'depositMutasi as total_uang_keluar' => function ($q) use ($req) {
                    $q->select(DB::raw("coalesce(sum(nilai),0)"));
                    $q->where('jenis_deposit', 'KREDIT');
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
                    $q->where('status', '!=', 'Cancel');
                    $q->where('keterangan', 'NOT LIKE', '%PEMBAYARAN MENGGUNAKAN DEPOSIT%');
                    $q->where('keterangan', 'NOT LIKE', '%PENGAMBILAN DEPOSIT%');
                    $q->where('keterangan', 'NOT LIKE', '%PENGAMBILAN SISA DEPOSIT%');
                }
            ])
            // ->with(['depositMutasi' => function ($query) {
            //     $query->select('deposit_id', 'nilai', 'created_at')
            //         ->orderBy('created_at', 'desc')
            //         ->limit(1); // Fetch the latest depositMutasi record for each deposit
            // }])
            ->where(function ($q) use ($req) {
                if ($req->owner_id != '') {
                    $q->where('owner_id', $req->owner_id);
                }

                if ($req->branch_id != '') {
                    $q->where('branch_id', $req->branch_id);
                }

                if (!Auth::user()->akses('global')) {
                    $q->where('branch_id', Auth::user()->branch_id);
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
            })->get();

        // dd($data);

        // $total_uang_masuk = $data->pluck('DepositMutasi')
        //     ->flatten()
        //     ->where('jenis_deposit', 'DEBET')
        //     ->sum('nilai');
        
        // $total_uang_keluar = $data->pluck('DepositMutasi')
        //     ->flatten()
        //     ->where('jenis_deposit', 'KREDIT')
        //     ->where('status', '!=', 'Cancel')
        //     ->sum('nilai');

        // $total_uang_masuk = $data->where('jenis_deposit', 'DEBET')->sum('nilai');

        // $total_uang_keluar = $data->where('jenis_deposit', 'KREDIT')->where('status', '!=', 'Cancel')->sum('nilai');

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
            // ->addColumn('icon', function ($data) {
            //     return '<i class="' . $data->icon . ' text-2xl"></i>';
            // })
            ->addColumn('nilai', function ($data){
                // return 'Rp. ' . ' ' . number_format($data->nilai);
                return 'Rp. ' . ' ' . number_format($data->total_uang_masuk);
            })
            ->addColumn('nilai_deposit', function ($data) {
                return 'Rp. ' . ' ' . number_format($data->nilai_deposit);
            })
            ->addColumn('sisa_deposit', function ($data) {
                return 'Rp. ' . ' ' . number_format($data->sisa_deposit);
            })
            // ->addColumn('kode', function ($data) {
            //     return $data->kode;
            // })
            ->addColumn('owner', function ($data) {
                return $data->Owner ? $data->Owner->name : '';
            })
            ->addColumn('kode_registrasi', function ($data) {
                return $data->Owner ? $data->Owner->kode : '';
            })
            ->addColumn('created_by', function ($data) {
                return $data->CreatedBy ? $data->CreatedBy->name : '-';
            })
            ->addColumn('updated_by', function ($data) {
                return $data->UpdatedBy ? $data->UpdatedBy->name : '-';
            })
            ->addColumn('branch', function ($data) {
                return $data->Branch != null ? $data->Branch->lokasi : "-";
            })
            // ->addColumn('total_uang_masuk', function ($data) {
            //     // if($data->jenis_deposit == 'DEBET'){
            //     //     return $data->nilai;
            //     // }else{
            //     //     return 0;
            //     // }
            //     return $data->total_uang_masuk;
            // })
            // ->addColumn('total_uang_keluar', function ($data) {
            //     // if($data->jenis_deposit == 'KREDIT' && $data->status != 'Cancel'){
            //     //     return $data->nilai;
            //     // }else{
            //     //     return 0;
            //     // }
            //     return $data->total_uang_keluar;
            // })
            ->rawColumns(['aksi', 'status'])
            // ->rawColumns(['aksi', 'status', 'icon', 'sequence'])
            // ->addIndexColumn()
            // ->make(true);
            ->toJson();
    }

    public function generateKode(Request $req)
    {
        $tanggal = Carbon::now()->format('dmY');
        $kode = 'DEPO-'  . $tanggal . '-';
        $sub = strlen($kode) + 1;
        $index = $this->model->deposit()
            ->selectRaw('max(CAST(substring(kode,' . $sub . ') as int)) as id')
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
                // DB::statement('LOCK TABLE t_jurnal, t_deposit_mutasi IN SHARE MODE');
                DB::statement('LOCK TABLE t_jurnal IN SHARE MODE');

                $input = $req->all();
                unset($input['_token']);
                $owner = $this->model->owner()->find($req->owner_id);
                $check = $this->model->deposit()
                    ->where('owner_id', $req->owner_id)
                    ->first();

                if ($check) {
                    $idDepositMutasi = $this->model->deposit_mutasi()->where('deposit_id', $check->id)->max('id') + 1;
                    $this->model->deposit_mutasi()
                        ->create([
                            'deposit_id'    => $check->id,
                            'id'    => $idDepositMutasi,
                            'jenis_deposit' => 'DEBET',
                            'branch_id' => Auth::user()->branch_id,
                            'nilai' => convertNumber($req->nilai_deposit),
                            'metode_pembayaran' => strtoupper($req->metode_pembayaran),
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'atas_nama'   => $req->atas_nama,
                            'keterangan'    => $req->keterangan,
                            'keterangan2'    => $req->keterangan2,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                    $nilai_deposit = convertNumber($req->nilai_deposit);
                    $this->model->deposit()
                        ->where('id', $check->id)
                        ->update([
                            'nilai_deposit' => reCalcDeposit($check->id),
                            'sisa_deposit' => reCalcDeposit($check->id),
                            'updated_by'    => me(),
                        ]);

                    // $idJurnal = $this->model->jurnal()->max('id') + 1;
                    $kodeJurnal = generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode;
                    // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);
                    $this->model->jurnal()
                        ->create([
                            // 'id'    => $idJurnal,
                            'kode'  => $kodeJurnal,
                            'branch_id' =>  Auth::user()->branch_id,
                            'tanggal'   => dateStore(),
                            'ref'   => $check->kode,
                            'metode_pembayaran' => strtoupper($req->metode_pembayaran),
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'jenis'   => 'DEPOSIT',
                            'dk'    => 'DEBET',
                            // 'description'    => 'PEMASUKAN DEPOSIT ATAS NO. REGISTRASI ' . $owner->kode,
                            'description'    => 'PEMASUKAN DEPOSIT ATAS NO. REGISTRASI ' . $owner->kode . ' ' . '(' . $owner->name .  ')',
                            'nominal'   => $nilai_deposit,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    DB::commit();
                    return Response()->json(['status' => 1, 'message' => 'Berhasil menambahkan deposit']);
                } else {
                    $nilai_deposit = convertNumber($req->nilai_deposit);
                    $input['id'] = $this->model->deposit()->max('id') + 1;
                    $input['name'] = ucwords($req->name);
                    $input['keterangan'] = ucwords($req->keterangan);
                    $input['keterangan2'] = ucwords($req->keterangan2);
                    $input['nilai_deposit'] = convertNumber($req->nilai_deposit);
                    $input['sisa_deposit'] = convertNumber($req->nilai_deposit);
                    $input['created_by'] = me();
                    $input['updated_by'] = me();
                    $input['branch_id'] = Auth::user()->branch_id;
                    $this->model->deposit()->create($input);

                    $this->model->deposit_mutasi()
                        ->create([
                            'deposit_id'    =>  $input['id'],
                            'id'    => 1,
                            'jenis_deposit' => 'DEBET',
                            'branch_id' => Auth::user()->branch_id,
                            'nilai' => $nilai_deposit,
                            'metode_pembayaran' => strtoupper($req->metode_pembayaran),
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'atas_nama'   => $req->atas_nama,
                            'keterangan'    => $req->keterangan,
                            'keterangan2'    => $req->keterangan2,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);

                    // $idJurnal = $this->model->jurnal()->max('id') + 1;
                    // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);
                    $this->model->jurnal()
                        ->create([
                            // 'id'    => $idJurnal,
                            'kode'  => generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode,
                            // 'kode'  => $kodeJurnal,
                            'branch_id' => Auth::user()->branch_id,
                            'metode_pembayaran' => strtoupper($req->metode_pembayaran),
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'tanggal'   => dateStore(),
                            'ref'   => $req->kode,
                            'jenis'   => 'DEPOSIT',
                            'dk'    => 'DEBET',
                            // 'description'    => 'PEMASUKAN DEPOSIT ATAS NO. REGISTRASI ' . $owner->kode . '(' .$owner->name.  ')',
                            'description'    => 'PEMASUKAN DEPOSIT ATAS NO. REGISTRASI ' . $owner->kode . ' ' . '(' . $owner->name .  ')',
                            'nominal'   => $nilai_deposit,
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                }

                DB::commit();

                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }   
        });
        // DB::commit();
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

        $data = $this->model->deposit()->where('id', $req->id)->first();

        // $data->sisa_deposit = reCalcDeposit($req->id);
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
                    'branch_id' => Auth::user()->branch_id,
                    'jenis_deposit' => 'KREDIT',
                    'nilai' => convertNumber($req->pengurangan),
                    'keterangan'    => $req->keterangan,
                    'keterangan2'    => $req->keterangan2,
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

                if ($req->metode_pembayaran == 'TUNAI') {
                    // $idJurnal = $this->model->jurnal()->max('id') + 1;
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
                            'metode_pembayaran' => strtoupper($req->metode_pembayaran),
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'atas_nama'   => $req->atas_nama,
                            'status'    => 'Done',
                            'created_by'    => me(),
                            'updated_by'    => me(),
                        ]);
                    
                    // $kodeJurnal = IdGenerator::generate(['table' => 't_jurnal', 'field' => 'kode', 'length' => 20, 'prefix' => "JR-" . Auth::user()->Branch->kode . '-' . Carbon::now()->format('Ymd') . '-', 'reset_on_prefix_change' => true]);

                    $this->model->jurnal()
                        ->create([
                            // 'id'    => $idJurnal,
                            'kode'  => generateKodeJurnal(Auth::user()->Branch->kode)->getData()->kode,
                            // 'kode'  => $kodeJurnal,
                            'branch_id' =>  Auth::user()->Branch->id,
                            'tanggal'   => dateStore(),
                            'ref'   => $check->kode,
                            'metode_pembayaran' => strtoupper($req->metode_pembayaran),
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'jenis'   => 'DEPOSIT',
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
                            'metode_pembayaran' => strtoupper($req->metode_pembayaran),
                            'nama_bank' => $req->nama_bank,
                            'nomor_kartu'   => $req->nomor_kartu,
                            'atas_nama'   => $req->atas_nama,
                            'keterangan'    => 'PENGAMBILAN DEPOSIT',
                            'keterangan2'    => $req->keterangan2,
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

                return Response()->json(['status' => 1, 'message' => 'Berhasil Penarikan Deposit']);
            } catch (QueryException $e) {
                DB::rollBack();
                throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
            }
        });
    }

    public function printHistoryDeposit($data)
    {

        $pdf = Pdf::loadView('transaksi/deposit/print_history', compact('data'))
            ->setPaper('a4', 'potrait');
        return $pdf->stream();
    }

    public function printHistory(Request $req)
    {
        $data = $this->model->deposit_mutasi()
            ->where('deposit_id', $req->deposit_id)
            ->where('id', $req->id)
            ->firstOrFail();

        if ($data->kasir) {
            return redirect()->route('printPembayaran', ['id' => $data->kasir->id]);
        } else {
            return $this->printHistoryDeposit($data);
        }
    }
}

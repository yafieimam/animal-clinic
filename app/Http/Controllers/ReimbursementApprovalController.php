<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ReimbursementApprovalController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        // Auth::user()->akses('view', null, true);
        return view('transaksi/reimbursement_approval/reimbursement_approval');
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        $approve = '';
        $reject = '';

        if (Auth::user()->akses('edit') && in_array($data->status, ['Waiting Approval', 'New'])) {
            $edit = '<li>' .
                '<a href="javascript:;" onclick="edit(\'' . $data->id . '\')" class="dropdown-item text-info">' .
                '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah' .
                '</a>' .
                '</li>';
        }

        if (Auth::user()->akses('delete') && in_array($data->status, ['Waiting Approval', 'New'])) {
            $delete =  '<li>' .
                '<a href="javascript:;" onclick="hapus(\'' . $data->id . '\')" class="dropdown-item text-danger">' .
                '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus' .
                '</a>' .
                '</li>';
        }

        if(Auth::user()->role_id == 10 && $data->status == 'Waiting Approval'){
            $approve =  '<li>' .
                '<a href="javascript:;" onclick="approve(\'' . $data->id . '\')" class="dropdown-item text-success">' .
                '<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;Approve' .
                '</a>' .
                '</li>';
            
            $reject =  '<li>' .
                '<a href="javascript:;" onclick="reject(\'' . $data->id . '\')" class="dropdown-item text-danger">' .
                '<i class="fa fa-times"></i>&nbsp;&nbsp;&nbsp;Reject' .
                '</a>' .
                '</li>';
        }

        return '<div class="dropdown">' .
            '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
            '<span class="w-5 h-5 flex items-center justify-center">' .
            '<i class="fa fa-bars"></i>' .
            '</span>' .
            '</button>' .
            '<div class="dropdown-menu w-40 ">' .
            '<ul class="dropdown-content">' .
            '<li>' .
            '<a href="javascript:;" onclick="lihat(\'' . $data->id . '\')" class="dropdown-item text-warning">' .
            '<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat' .
            '</a>' .
            '</li>' .
            $edit .
            $delete .
            $approve .
            $reject .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $query = $this->model->reimbursement()
            ->whereHas('CreatedBy', function ($q) {
                if(Auth::user()->role_id == 10){
                    $q->where('branch_id', Auth::user()->branch_id);
                }else{
                    $q->where('id', Auth::user()->id);
                }
            })
            ->where(function ($q) {
                if (Auth::user()->role_id == 10) {
                    $q->where('status', '!=', 'New');
                }
            });
        
        if (Auth::user()->role_id == 10) {
            $query->orderByRaw("CASE WHEN status = 'Waiting Approval' THEN 0 ELSE 1 END")
                    ->orderBy('status', 'asc');
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            ->addColumn('status', function ($data) {
                if ($data->status == 'New') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-secondary text-white cursor-pointer font-medium">New</div>';
                } elseif ($data->status == 'Waiting Approval') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">Waiting Approval</div>';
                } elseif ($data->status == 'Sedang Diproses') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium">Sedang Diproses</div>';
                } elseif ($data->status == 'Sudah Dibayar') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Sudah Dibayar</div>';
                } elseif ($data->status == 'Ditolak') {
                    return '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Ditolak</div>';
                }
            })
            ->addColumn('nama_klaim', function ($data) {
                return ucwords($data->nama_klaim);
            })
            ->addColumn('nama_karyawan', function ($data) {
                return $data->CreatedBy ? $data->CreatedBy->name : '-';
            })
            ->rawColumns(['aksi', 'status'])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);


            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                $idData = $this->model->reimbursement()->max('id') + 1;

                $input['id'] = $idData;
                $input['name'] = ucwords($req->nama_klaim);
                $input['tanggal'] = $req->tanggal;
                $input['tipe_klaim'] = $req->tipe_klaim;
                $input['jumlah_biaya'] = convertNumber($req->jumlah_biaya);
                $input['keterangan'] = ucwords($req->keterangan);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = 'New';

                // Auth::user()->akses('create', null, true);
                $this->model->reimbursement()->create($input);

                foreach ($req->file('file_data') as $index => $file) {
                    if ($file) {
                        $path = 'image/reimbursement_klaim';
                        $id =  Str::uuid($idData)->toString();
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

                    $this->model->reimbursementFileKlaim()
                        ->create([
                            'reimbursement_id' => $idData,
                            'id'    => $req->seq_data[$index],
                            'file'  => $foto,
                            'created_by' => me(),
                            'updated_by' => me()
                        ]);
                }

                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                // Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->nama_klaim);
                $input['tanggal'] = $req->tanggal;
                $input['tipe_klaim'] = $req->tipe_klaim;
                $input['jumlah_biaya'] = convertNumber($req->jumlah_biaya);
                $input['keterangan'] = ucwords($req->keterangan);
                $input['updated_by'] = me();

                $this->model->reimbursement()->find($req->id)->update($input);

                foreach ($req->file('file_data') as $index => $file) {
                    $dataReimbursement = $this->model->reimbursementFileKlaim()
                        ->where('reimbursement_id', $req->id)
                        ->where('id', $req->seq_data[$index])->first();

                    if($dataReimbursement){
                        if (is_file($dataReimbursement->file)) {
                            unlink($dataReimbursement->file);
                        }

                        if ($file) {
                            $path = 'image/reimbursement_klaim';
                            $id =  Str::uuid($req->id)->toString();
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

                        $this->model->reimbursementFileKlaim()
                            ->where('reimbursement_id', $req->id)
                            ->where('id', $req->seq_data[$index])
                            ->update([
                                'file'  => $foto
                            ]);
                    }else{
                        if ($file) {
                            $path = 'image/reimbursement_klaim';
                            $id =  Str::uuid($req->id)->toString();
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

                        $this->model->reimbursementFileKlaim()
                            ->create([
                                'reimbursement_id' => $req->id,
                                'id'    => $req->seq_data[$index],
                                'file'  => $foto,
                                'created_by' => me(),
                                'updated_by' => me()
                            ]);
                    }
                }

                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function submit(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $input = $req->all();
            unset($input['_token']);


            if ($req->id == null or $req->id == 'null' or $req->id == '') {
                $idData = $this->model->reimbursement()->max('id') + 1;

                $input['id'] = $idData;
                $input['name'] = ucwords($req->nama_klaim);
                $input['tanggal'] = $req->tanggal;
                $input['tipe_klaim'] = $req->tipe_klaim;
                $input['jumlah_biaya'] = convertNumber($req->jumlah_biaya);
                $input['keterangan'] = ucwords($req->keterangan);
                $input['created_by'] = me();
                $input['updated_by'] = me();
                $input['status'] = 'Waiting Approval';

                // Auth::user()->akses('create', null, true);
                $this->model->reimbursement()->create($input);

                foreach ($req->file('file_data') as $index => $file) {
                    if ($file) {
                        $path = 'image/reimbursement_klaim';
                        $id =  Str::uuid($idData)->toString();
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

                    $this->model->reimbursementFileKlaim()
                        ->create([
                            'reimbursement_id' => $idData,
                            'id'    => $req->seq_data[$index],
                            'file'  => $foto,
                            'created_by' => me(),
                            'updated_by' => me()
                        ]);
                }

                return Response()->json(['status' => 1, 'message' => 'Data berhasil disimpan']);
            } else {
                // Auth::user()->akses('edit', null, true);
                $input['name'] = ucwords($req->nama_klaim);
                $input['tanggal'] = $req->tanggal;
                $input['tipe_klaim'] = $req->tipe_klaim;
                $input['jumlah_biaya'] = convertNumber($req->jumlah_biaya);
                $input['keterangan'] = ucwords($req->keterangan);
                $input['updated_by'] = me();
                $input['status'] = 'Waiting Approval';

                $this->model->reimbursement()->find($req->id)->update($input);

                $fileData = $req->file('file_data');
                if ($fileData != null) {
                    foreach ($fileData as $index => $file) {
                        $dataReimbursement = $this->model->reimbursementFileKlaim()
                            ->where('reimbursement_id', $req->id)
                            ->where('id', $req->seq_data[$index])->first();

                        if($dataReimbursement){
                            if (is_file($dataReimbursement->file)) {
                                unlink($dataReimbursement->file);
                            }

                            if ($file) {
                                $path = 'image/reimbursement_klaim';
                                $id =  Str::uuid($req->id)->toString();
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

                            $this->model->reimbursementFileKlaim()
                                ->where('reimbursement_id', $req->id)
                                ->where('id', $req->seq_data[$index])
                                ->update([
                                    'file'  => $foto
                                ]);
                        }else{
                            if ($file) {
                                $path = 'image/reimbursement_klaim';
                                $id =  Str::uuid($req->id)->toString();
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

                            $this->model->reimbursementFileKlaim()
                                ->create([
                                    'reimbursement_id' => $req->id,
                                    'id'    => $req->seq_data[$index],
                                    'file'  => $foto,
                                    'created_by' => me(),
                                    'updated_by' => me()
                                ]);
                        }
                    }
                }

                return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
            }
        });
    }

    public function edit(Request $req)
    {
        // if (!isset($req->param)) {
        //     Auth::user()->akses('edit', null, true);
        // }
        $data = $this->model->reimbursement()
            ->with(['reimbursementFileKlaim', 'reimbursementFileApproval'])
            ->where('id', $req->id)->get();
        return Response()->json(['status' => 1, 'data' => $data[0]]);
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            // Auth::user()->akses('delete', null, true);

            $this->model->reimbursement()
                ->where('id', $req->id)
                ->delete();

            $data = $this->model->reimbursementFileKlaim()
                ->where('reimbursement_id', $req->id)->get();

            foreach ($data as $index => $file) {
                if (is_file($file['file'])) {
                    unlink($file['file']);
                }
            }

            $this->model->reimbursementFileKlaim()
                ->where('reimbursement_id', $req->id)
                ->delete();

            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }

    public function approve(Request $req)
    {
        // Auth::user()->akses('validation', null, true);
        if(Auth::user()->role_id != 10){
            abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
        }
        return DB::transaction(function () use ($req) {
            $this->model->reimbursement()->where('id', $req->id)
                ->update([
                    'status' => 'Sedang Diproses'
                ]);
            return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }

    public function reject(Request $req)
    {
        // Auth::user()->akses('validation', null, true);
        if(Auth::user()->role_id != 10){
            abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
        }
        return DB::transaction(function () use ($req) {
            $this->model->reimbursement()->where('id', $req->id)
                ->update([
                    'keterangan_approval' => ucwords($req->keterangan_approval),
                    'status' => 'Ditolak'
                ]);
            return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Barryvdh\DomPDF\Facade as PDF;

class ReimbursementApprovedController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        // Auth::user()->akses('view', null, true);
        if(!in_array(Auth::user()->role_id, [3, 9])){
            abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
        }
        return view('transaksi/reimbursement_approved/reimbursement_approved');
    }

    public function aksi($data)
    {
        $approve = '';
        $reject = '';
        $print = '';

        if(in_array(Auth::user()->role_id, [3, 9]) && $data->status == 'Sedang Diproses'){
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

        if(in_array(Auth::user()->role_id, [3, 9]) && $data->status == 'Sudah Dibayar'){
            $print =  '<li>' .
                '<a href="javascript:;" onclick="printReimbursement(\'' . $data->id . '\')" class="dropdown-item text-info">' .
                '<i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Print' .
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
            $approve .
            $reject .
            $print .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req)
    {
        $data = $this->model->reimbursement()
            ->whereIn('status', ['Sedang Diproses', 'Sudah Dibayar'])
            ->orderByRaw("CASE WHEN status = 'Sedang Diproses' THEN 0 ELSE 1 END")
            ->orderBy('status', 'asc')
            ->get();

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

            if(!in_array(Auth::user()->role_id, [3, 9])){
                abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
            }
            $input['keterangan_approval'] = ucwords($req->keterangan_approval);
            $input['status'] = 'Sudah Dibayar';
            $input['updated_by'] = me();

            $this->model->reimbursement()->find($req->id)->update($input);

            $fileData = $req->file('file_data');
            if ($fileData != null) {
                foreach ($fileData as $index => $file) {
                    
                    if ($file) {
                        $path = 'image/reimbursement_approval';
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

                    $this->model->reimbursementFileApproval()
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
        });
    }

    public function edit(Request $req)
    {
        if(!in_array(Auth::user()->role_id, [3, 9])){
            abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
        }

        $data = $this->model->reimbursement()
            ->with(['reimbursementFileKlaim', 'reimbursementFileApproval'])
            ->where('id', $req->id)->get();
        return Response()->json(['status' => 1, 'data' => $data[0]]);
    }

    public function reject(Request $req)
    {
        // Auth::user()->akses('validation', null, true);
        if(!in_array(Auth::user()->role_id, [3, 9])){
            abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
        }
        return DB::transaction(function () use ($req) {
            $this->model->reimbursement()->where('id', $req->id)
                ->update([
                    'keterangan_approval' => ucwords($req->keterangan_approval),
                    'status' => 'Ditolak'
                ]);

            $fileData = $req->file('file_data');
            if ($fileData != null) {
                foreach ($fileData as $index => $file) {
                    
                    if ($file) {
                        $path = 'image/reimbursement_approval';
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

                    $this->model->reimbursementFileApproval()
                        ->create([
                            'reimbursement_id' => $req->id,
                            'id'    => $req->seq_data[$index],
                            'file'  => $foto,
                            'created_by' => me(),
                            'updated_by' => me()
                        ]);
                }
            }
            
            return Response()->json(['status' => 1, 'message' => 'Status berhasil diubah']);
        });
    }

    public function print(Request $req)
    {
        $data = $this->model->reimbursement()
            ->with(['CreatedBy', 'UpdatedBy'])
            ->find($req->id);

        $nama = 'E-KLAIM ' . $data->name . ' A.N. ' . $data->CreatedBy->name . '-' . Carbon::parse($data->tanggal)->format('Y-m-d') . '.pdf';

        $pdf = PDF::loadView('transaksi/reimbursement_approved/print', compact('data'))
            ->setPaper('a4', 'potrait');
        return $pdf->stream($nama);
    }
}

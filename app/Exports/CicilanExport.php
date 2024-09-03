<?php

namespace App\Exports;

use App\Models\Kasir;
use App\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class CicilanExport implements FromView
{
    public $req;
    public function __construct(Request $request)
    {
        $this->req = $request;
    }

    public function view(): View
    {
        $data = Kasir::
            where(function ($q)  {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->where('branch_id', Auth::user()->branch_id);
                } else {
                    if ($this->req->branch_id_filter != '') {
                        $q->where('branch_id', $this->req->branch_id_filter);
                    }
                }

                if ($this->req->tanggal_awal != '') {
                    $q->whereDate('updated_at', '>=', $this->req->tanggal_awal);
                }else{
                    $q->whereDate('updated_at', '>=', date('Y-m-d'));
                }

                if ($this->req->tanggal_akhir != '') {
                    $q->whereDate('updated_at', '<=', $this->req->tanggal_akhir);
                }else{
                    $q->whereDate('updated_at', '<=', date('Y-m-d'));
                }

                if ($this->req->owner_id_filter != '') {
                    $q->where('owner_id', $this->req->owner_id_filter);
                }

                if ($this->req->type_kasir_filter != '') {
                    $q->where('type_kasir', $this->req->type_kasir_filter);
                }
            })
            ->where('sisa_pelunasan', '!=', '0')
            ->orderBy('updated_at', 'DESC')
            ->get();

        $total_hutang = $data->where('sisa_pelunasan', '>' , 0)->sum('pembayaran');

        return view('transaksi/cicilan/excel_cicilan', [
            'data' => $data,
            'total_hutang' => $total_hutang,
        ]);
    }
}

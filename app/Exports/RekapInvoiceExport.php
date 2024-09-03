<?php

namespace App\Exports;

use App\Models\Kasir;
use App\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class RekapInvoiceExport implements FromView
{
    public $req;
    public function __construct(Request $request)
    {
        $this->req = $request;
    }

    public function view(): View
    {
        $data = Kasir::where(function ($q) {
            if (!Auth::user()->akses('global', null, false)) {
                $q->where('branch_id', Auth::user()->branch_id);
            }
            if ($this->req->tanggal_awal != '') {
                $q->whereDate('created_at', '>=', $this->req->tanggal_awal);
            }

            if ($this->req->tanggal_akhir != '') {
                $q->whereDate('created_at', '<=', $this->req->tanggal_akhir);
            }
            if ($this->req->owner_id != '') {
                $q->where('owner_id', $this->req->owner_id);
            }
            if ($this->req->langsung_lunas != '') {
                if ($this->req->langsung_lunas == 't') {
                    $q->where('sisa_pelunasan', 0);
                }else{
                    $q->where('sisa_pelunasan', '>' , 0);
                }
            }
        })
        ->whereDate('created_at', '>=', $this->req->tanggal_awal)
        ->where('type_kasir', '=', 'Normal')
        ->get();

        $total_lunas = $data->where('sisa_pelunasan',0)->sum('pembayaran');
        $total_hutang = $data->where('sisa_pelunasan', '>' ,0)->sum('pembayaran');

        return view('transaksi/rekap_invoice/excel_rekap_invoice', [
            'data' => $data,
            'total_lunas' => $total_lunas,
            'total_hutang' => $total_hutang,
        ]);
    }
}

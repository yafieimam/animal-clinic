<?php

namespace App\Exports;

use App\Models\Kasir;
use App\Models\KasirPembayaran;
use App\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class HibahExport implements FromView
{
    public $req;
    public function __construct(Request $request)
    {
        $this->req = $request;
    }

    public function view(): View
    {
        $data = KasirPembayaran::
            with(['kasir'])
            ->where(function ($q)  {
                if (!Auth::user()->akses('global', null, false)) {
                    $q->whereHas('kasir', function ($subQ) {
                        $subQ->where('branch_id', Auth::user()->branch_id);
                    });
                } else {
                    if ($this->req->branch_id_filter != '') {
                        $q->whereHas('kasir', function ($subQ) {
                            $subQ->where('branch_id', $this->req->branch_id_filter);
                        });
                    }
                }

                if ($this->req->tanggal_awal != '') {
                    $q->whereDate('updated_at', '>=', $this->req->tanggal_awal);
                }

                if ($this->req->tanggal_akhir != '') {
                    $q->whereDate('updated_at', '<=', $this->req->tanggal_akhir);
                }

                if ($this->req->owner_id != '') {
                    $q->whereHas('kasir', function ($subQ) {
                        $subQ->where('owner_id', $this->req->owner_id);
                    });
                }

                if ($this->req->type_kasir_filter != '') {
                    $q->whereHas('kasir', function ($subQ) {
                        $subQ->where('type_kasir', $this->req->type_kasir_filter);
                    });
                }
            })
            ->where('jenis_pembayaran', 'HIBAH') 
            ->get();
            $hibah = $data->sum('nilai_pembayaran');

        return view('transaksi/hibah/excel_hibah', [
            'data' => $data,
            'hibah' => $hibah,
        ]);
    }
}

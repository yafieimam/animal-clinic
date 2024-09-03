<?php

namespace App\Exports;

use App\Models\KamarRawatInapDanBedahDetail;
use App\Models\Pasien;
use App\Models\RekamMedisRekomendasiTindakanBedah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KamarExport implements FromView
{
    public $data;
    public function __construct(Request $req)
    {
        $this->data = $req;
    }
    public function view(): View
    {

        $data = KamarRawatInapDanBedahDetail::where('status_pindah', true)
            ->where('status', 'Done')
            // ->withCount('')
            // ->with([
            //     'jumlah_kamar' => function ($q) {
            //         $q->where('status_pindah', false);
            //     }
            // ])
            ->get();

        return view('exports.excel_kamar', [
            'data' => $data,
        ]);
    }
}

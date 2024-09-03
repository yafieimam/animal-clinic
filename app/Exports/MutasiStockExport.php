<?php

namespace App\Exports;

use App\Models\MutasiStock;
use App\Models\Pasien;
use App\Models\RekamMedisRekomendasiTindakanBedah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MutasiStockExport implements FromView
{
    public $data;
    public function __construct(Request $req)
    {
        $this->data = $req;
    }
    public function view(): View
    {
        $data = MutasiStock::whereHas('stock', function ($q) {
            if ($this->data->branch_id != '') {
                $q->where('branch_id', $this->data->branch_id);
            }

            if ($this->data->jenis_item != '') {
                if ($this->data->jenis_item == 'NON OBAT') {
                    if ($this->data->item_id != '') {
                        $q->where('item_non_obat_id', $this->data->item_id);
                    }
                    $q->has('ItemNonObat');
                } else {
                    if ($this->data->item_id != '') {
                        $q->where('produk_obat_id', $this->data->item_id);
                    }
                    $q->has('ProdukObat');
                }
            }
        })->where(function ($q) {
            $q->where('created_at', '>=', $this->data->tanggal_awal);
            $q->where('created_at', '<=', $this->data->tanggal_akhir);
        })->orderBy('created_at', 'ASC')->get();

        return view('exports.excel_mutasi_stock', [
            'data' => $data,
        ]);
    }
}

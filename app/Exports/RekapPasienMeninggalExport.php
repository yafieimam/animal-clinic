<?php

namespace App\Exports;

use App\Models\Pasien;
use App\Models\RekamMedisPasien;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RekapPasienMeninggalExport implements FromView
{
    public $data;
    public function __construct(Request $req)
    {
        $this->data = $req;
    }
    public function view(): View
    {

        $data = RekamMedisPasien::whereHas('pasienMeninggal', function ($q) {
            if ($this->data->tanggal_awal != '') {
                $q->whereDate('created_at', '>=', $this->data->tanggal_awal);
            }

            if ($this->data->tanggal_akhir != '') {
                $q->whereDate('created_at', '<=', $this->data->tanggal_akhir);
            }
        })->whereHas('Pendaftaran', function ($q) {
            if ($this->data->dokter_poli != '') {
                $q->where('dokter', $this->data->dokter_poli);
            }
        })->whereHas('kamarRawatInapDanBedahDetailFirst', function ($q) {
            if ($this->data->kamar_rawat_inap_dan_bedah_id != '') {
                $q->where('status_pindah', false);
                $q->where('kamar_rawat_inap_dan_bedah_id', $this->data->kamar_rawat_inap_dan_bedah_id);
            }
        })->with([
            'kamarRawatInapDanBedahDetailFirst' => function ($q) {
                $q->where('status_pindah', false);
                if ($this->data->kamar_rawat_inap_dan_bedah_id != '') {
                    $q->where('kamar_rawat_inap_dan_bedah_id', $this->data->kamar_rawat_inap_dan_bedah_id);
                }
            },
            'pasienMeninggal',
            'pasien',
        ])->get();

        return view('exports.excel_rekap_pasien_meninggal', [
            'data' => $data,
        ]);
    }
}

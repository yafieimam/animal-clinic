<?php

namespace App\Exports;

use App\Models\Pasien;
use App\Models\RekamMedisRekomendasiTindakanBedah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BedahExport implements FromView
{
    public $data;
    public function __construct(Request $req)
    {
        $this->data = $req;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'use_bom' => false,
            'output_encoding' => 'ISO-8859-1',
        ];
    }

    public function view(): View
    {

        $data = RekamMedisRekomendasiTindakanBedah::whereHas('RekamMedisPasien', function ($q) {
            $q->whereHas('Pendaftaran', function ($q) {
                $q->where('branch_id', Auth::user()->branch_id);
            });
        })
            ->where(function ($q) {
                if ($this->data->tindakan_id_filter != '') {
                    $q->where('tindakan_id', $this->data->tindakan_id_filter);
                }

                if ($this->data->rekomendasi_tanggal_bedah != '') {
                    $q->where('tanggal_rekomendasi_bedah', $this->data->rekomendasi_tanggal_bedah);
                }

                if ($this->data->ruangan_rawat_inap != '') {
                    $q->whereHas('RekamMedisPasien', function ($q) {
                        $q->whereHas('KamarRawatInapDanBedahDetail', function ($q) {
                            $q->where('kamar_rawat_inap_dan_bedah_id', $this->data->ruangan_rawat_inap);
                        });
                    });
                }


                if ($this->data->branch_id != '') {
                    $q->whereHas('RekamMedisPasien', function ($q) {
                        $q->whereHas('pendaftaran', function ($q) {
                            $q->where('branch_id', $this->data->branch_id);
                        });
                    });
                }
            })
            ->with([
                'RekamMedisPasien' => function ($q) {
                    $q->with([
                        'KamarRawatInapDanBedahDetail'
                    ]);
                }
            ])
            // ->where('status', 'Released')
            ->get();

        return view('exports.excel_bedah', [
            'data' => $data,
        ]);
    }
}

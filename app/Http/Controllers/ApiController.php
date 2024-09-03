<?php

namespace App\Http\Controllers;

use App\Exports\KamarExport;
use App\Models\Modeler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ApiController extends Controller
{

    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function generateTanggalKamar(Request $req)
    {
        $check = $this->model->kamarRawatInapDanBedahDetail()
            ->where('status_pindah', true)
            ->where('status', 'Done')
            ->with([
                'jumlah_kamar' => function ($q) {
                    $q->where('status_pindah', false);
                },
                'pasien' => function ($q) {
                    $q->select('id', 'name');
                },
            ])
            ->get();

        foreach ($check as $key => $value) {
            $this->model->kamarRawatInapDanBedahDetail()
                ->where('kamar_rawat_inap_dan_bedah_id', $value->kamar_rawat_inap_dan_bedah_id)
                ->where('id', $value->id)
                ->update([
                    'tanggal_keluar' => $value->jumlah_kamar->tanggal_masuk,
                ]);
        }

        // return Excel::download(new KamarExport($req), 'list_kamar_salah.xlsx');


        return $check;
    }

    function deleteJournal(Request $req): JsonResponse
    {
        $this->model->jurnal()->findOrFail($req->id)->delete();
        $this->model->jurnalDetail()->where('jurnal_id', $req->id)->delete();
        return response()->json(['status' => 1, 'message' => "Data has been deleted"]);
    }
}

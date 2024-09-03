<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class KamarRawatInapDanBedahDetail extends Model
{
    use Loggable;

    protected $table = "mka_kamar_rawat_inap_dan_bedah_detail";
    protected $primaryKey = 'id';

    protected $fillable = [
        'kamar_rawat_inap_dan_bedah_id',
        'id',
        'pasien_id',
        'rekam_medis_pasien_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
        'status_pindah',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function KamarRawatInapDanBedah()
    {
        return $this->belongsTo(KamarRawatInapDanBedah::class);
    }

    public function Pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }

    public function jumlah_kamar()
    {
        return $this->belongsTo(KamarRawatInapDanBedahDetail::class, 'rekam_medis_pasien_id', 'rekam_medis_pasien_id');
    }
}

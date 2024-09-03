<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class RekamMedisResepRacikan extends Model
{
    use Loggable;
    use \Awobaz\Compoships\Compoships;

    protected $table = "mp_rekam_medis_resep_racikan";
    protected $primaryKey = 'rekam_medis_pasien_id';

    protected $fillable = [
        'rekam_medis_pasien_id',
        'rekam_medis_resep_id',
        'id',
        'produk_obat_id',
        'qty',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }

    public function ProdukObat()
    {
        return $this->belongsTo(ProdukObat::class);
    }

    public function RekamMedisResep()
    {
        return $this->belongsTo(RekamMedisResep::class, ['rekam_medis_pasien_id', 'rekam_medis_resep_id'], ['rekam_medis_pasien_id', 'id']);
    }
}

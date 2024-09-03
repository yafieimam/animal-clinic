<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedisPasienMutasiStock extends Model
{
    use Loggable;

    protected $table = "mp_rekam_medis_pasien_mutasi_stock";
    protected $primaryKey = 'rekam_medis_pasien_id';

    protected $fillable = [
        'rekam_medis_pasien_id',
        'id',
        'fitur_id',
        'tipe_fitur',
        'mutasi_stock_id',
        'harga_satuan',
        'qty',
        'total_harga',
        'created_at',
        'updated_at'
    ];

    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }
}

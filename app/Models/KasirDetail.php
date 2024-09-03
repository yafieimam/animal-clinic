<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class KasirDetail extends Model
{
    use Loggable;

    protected $table = "t_kasir_detail";
    protected $primaryKey =  'kasir_id';

    protected $fillable = [
        'kasir_id',
        'id',
        'table',
        'ref',
        'stock',
        'harga',
        'qty',
        'bruto',
        'jenis_stock',
        'pasien_id',
        'diskon_penyesuaian',
        'nilai_diskon_penyesuaian',
        'sub_total',
        'rekam_medis_pasien_id',
        'description',
        'created_at',
        'updated_at'
    ];

    public function Kasir()
    {
        return $this->belongsTo(Kasir::class);
    }

    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id', 'id');
    }

    public function ItemNonObat()
    {
        return $this->belongsTo(ItemNonObat::class, 'ref', 'id');
    }
}

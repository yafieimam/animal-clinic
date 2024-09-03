<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedisPakan extends Model
{
    use Loggable;

    protected $table = "mp_rekam_medis_pakan";
    protected $primaryKey = 'rekam_medis_pasien_id';
    public $incrementing = false;
    protected $fillable = [
        'rekam_medis_pasien_id',
        'id',
        'item_non_obat_id',
        'jumlah',
        'harga_jual',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }

    public function ItemNonObat()
    {
        return $this->belongsTo(ItemNonObat::class);
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedisKondisiHarian extends Model
{
    use Loggable;

    protected $table = "mp_rekam_medis_kondisi_harian";
    protected $primaryKey = 'rekam_medis_pasien_id';
    public $incrementing = false;
    protected $fillable = [
        'rekam_medis_pasien_id',
        'id',
        'suhu',
        'makan',
        'minum',
        'urin',
        'feses',
        'keterangan',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

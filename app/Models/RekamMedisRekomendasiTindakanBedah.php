<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedisRekomendasiTindakanBedah extends Model
{
    use Loggable;

    protected $table = "mp_rekam_medis_rekomendasi_tindakan_bedah";
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'rekam_medis_pasien_id',
        'id',
        'tindakan_id',
        'tanggal_rekomendasi_bedah',
        'status',
        'status_urgensi',
        'upload_form_persetujuan',
        'keterangan',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tanggal_rekomendasi_bedah' => 'date:d-M-Y',
    ];


    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }

    public function Tindakan()
    {
        return $this->belongsTo(Tindakan::class);
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function UpdatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}

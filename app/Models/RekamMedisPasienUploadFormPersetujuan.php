<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedisPasienUploadFormPersetujuan extends Model
{
    use Loggable;

    protected $table = "mp_rekam_medis_pasien_upload_form_persetujuan";
    protected $primaryKey = 'rekam_medis_pasien_id';

    protected $fillable = [
        'rekam_medis_pasien_id',
        'id',
        'file',
        'created_at',
        'updated_at'
    ];

    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }
}

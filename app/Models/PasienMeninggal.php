<?php

namespace App\Models;

use DateTimeInterface;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasienMeninggal extends Model
{
    use HasFactory;
    use Loggable;

    protected $table = "mp_pasien_meninggal";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'pasien_id',
        'meninggal_saat',
        'rekam_medis_pasien_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];


    public function Pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function rekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i');
    }
}

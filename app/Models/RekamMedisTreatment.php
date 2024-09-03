<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class RekamMedisTreatment extends Model
{
    use Loggable;

    protected $table = "mp_rekam_medis_treatment";
    protected $primaryKey = 'rekam_medis_pasien_id';

    protected $fillable = [
        'rekam_medis_pasien_id',
        'id',
        'treatment',
        'tarif',
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

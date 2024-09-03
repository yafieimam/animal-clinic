<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class RekamMedisLogHistory extends Model
{
    use Loggable;

    protected $table = "mp_rekam_medis_log_history";
    protected $primaryKey = 'rekam_medis_pasien_id';

    protected $fillable = [
        'rekam_medis_pasien_id',
        'id',
        'description',
        'table',
        'ref_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}

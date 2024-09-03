<?php

namespace App\Models;

use App\Models\Core\Traits\ResepTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogResep extends Model
{

    protected $table = "log_resep";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'rekam_medis_resep_id',
        'rekam_medis_pasien_id',
        'jenis',
        'url',
        'user_id',
        'created_at',
        'updated_at'
    ];
}

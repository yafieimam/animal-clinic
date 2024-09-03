<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumumanKaryawan extends Model
{
    use HasFactory;

    use Loggable;

    protected $table = "mka_pengumuman_karyawan";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}

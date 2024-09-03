<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class Role extends Model
{
    use Loggable;

    protected $table = "s_role";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'type_role',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public static $enumTypeRole = [
        'DOKTER',
        'KARYAWAN',
        'APOTEKER',
    ];
}

<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class HakAkses extends Model
{
    use Loggable;

    protected $table = "s_hak_akses";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'role_id',
        'menu_id',
        'view',
        'create',
        'edit',
        'delete',
        'global',
        'print',
        'validation',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}

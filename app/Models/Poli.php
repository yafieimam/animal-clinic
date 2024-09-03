<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class Poli extends Model
{
    use Loggable;

    protected $table = "mk_poli";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'description',
        'open_time',
        'close_time',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}

<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    use Loggable;

    protected $table = "mk_bagian";
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

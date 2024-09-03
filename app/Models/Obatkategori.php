<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Obatkategori extends Model
{
    use Loggable;
    use SoftDeletes;

    protected $table = "obatkategori";
    protected $primaryKey = 'obatkategori_id';

    protected $fillable = [
        'obatkategori_id',
        'obatkategori_name'
    ];
}
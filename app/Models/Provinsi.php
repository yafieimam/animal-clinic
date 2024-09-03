<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use Loggable;

    protected $table = "indonesia_provinces";
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'meta',
        'created_at',
        'updated_at'
    ];

    public function Kota()
    {
        return $this->hasMany(indonesia_cities::class, 'city_id', 'id');
    }
}

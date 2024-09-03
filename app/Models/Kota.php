<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    protected $table = "indonesia_cities";
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'province_id',
        'name',
        'meta',
        'created_at',
        'updated_at'
    ];

    public function Provinsi()
    {
        return $this->belongsTo(indonesia_provinces::class, 'province_id', 'id');
    }

    public function Kecamatan()
    {
        return $this->hasMany(indonesia_districts::class, 'city_id', 'id');
    }
}

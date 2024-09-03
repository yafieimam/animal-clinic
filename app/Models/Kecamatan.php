<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = "indonesia_districts";
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'city_id',
        'name',
        'meta',
        'created_at',
        'updated_at'
    ];

    public function Kota()
    {
        return $this->belongsTo(indonesia_cities::class, 'city_id', 'id');
    }

    public function Kelurahan()
    {
        return $this->hasMany(indonesia_villages::class, 'district_id', 'id');
    }
}

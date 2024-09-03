<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $table = "indonesia_villages";
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'district_id',
        'name',
        'meta',
        'created_at',
        'updated_at'
    ];

    public function Kecamatan()
    {
        return $this->belongsTo(indonesia_districts::class, 'district_id', 'id');
    }
}

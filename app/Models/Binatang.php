<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class Binatang extends Model
{
    use Loggable;

    protected $table = "mk_binatang";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function ras()
    {
        return $this->hasMany(Ras::class);
    }

    public function RekamMedisPasien()
    {
        return $this->hasManyThrough(
            RekamMedisPasien::class,
            Pasien::class,
            'binatang_id', // Foreign key on do table...
            'pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }
}

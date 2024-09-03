<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class Tindakan extends Model
{
    use Loggable;

    protected $table = "mk_tindakan";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'binatang_id',
        'poli_id',
        'tarif',
        'description',
        'diskon',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function binatang()
    {
        return $this->belongsTo(Binatang::class);
    }

    public function rekamMedisRekomendasiTindakanBedah()
    {
        return $this->hasMany(RekamMedisRekomendasiTindakanBedah::class);
    }

    public function rekamMedisTindakan()
    {
        return $this->hasMany(RekamMedisTindakan::class);
    }
}

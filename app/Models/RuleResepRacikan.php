<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class RuleResepRacikan extends Model
{
    use Loggable;
    protected $table = "mo_rule_resep_racikan";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kategori_obat_id',
        'min',
        'symbol',
        'max',
        'satuan',
        'harga',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public static $enumSatuan = [
        'BERAT',
        'TINGGI',
        'UMUR',
    ];

    public function binatang()
    {
        return $this->belongsTo(Binatang::class);
    }

    public function KategoriObat()
    {
        return $this->belongsTo(KategoriObat::class);
    }
}

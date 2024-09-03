<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class KamarRawatInapDanBedah extends Model
{
    use Loggable;

    protected $table = "mka_kamar_rawat_inap_dan_bedah";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'kategori_kamar_id',
        'branch_id',
        'kapasitas',
        'tarif_per_hari',
        'description',
        'status',
        'diskon',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function KategoriKamar()
    {
        return $this->belongsTo(KategoriKamar::class);
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function KamarRawatInapDanBedahDetail()
    {
        return $this->hasMany(KamarRawatInapDanBedahDetail::class);
    }
}

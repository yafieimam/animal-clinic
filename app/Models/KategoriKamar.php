<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class KategoriKamar extends Model
{
    use Loggable;

    protected $table = "mka_kategori_kamar";
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

    public function KamarRawatInapDanBedah()
    {
        return $this->hasMany(KamarRawatInapDanBedah::class);
    }
}

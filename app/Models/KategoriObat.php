<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class KategoriObat extends Model
{
    use Loggable;

    protected $table = "mo_kategori_obat";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'diskon',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function ProdukObat()
    {
        return $this->hasMany('\App\ProdukObat');
    }
}

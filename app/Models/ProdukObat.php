<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class ProdukObat extends Model
{
    use Loggable;

    protected $table = "mo_produk_obat";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'name',
        'dosis',
        'kategori_obat_id',
        'type_obat_id',
        'satuan_obat_id',
        'harga',
        'description',
        'diskon',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function SatuanObat()
    {
        return $this->belongsTo(SatuanObat::class);
    }

    public function TypeObat()
    {
        return $this->belongsTo(TypeObat::class);
    }

    public function Satuan()
    {
        return $this->belongsTo(SatuanObat::class, 'satuan_obat_id', 'id');
    }

    public function Type()
    {
        return $this->belongsTo(TypeObat::class);
    }

    public function Stock()
    {
        return $this->hasMany(Stock::class, 'produk_obat_id', 'id');
    }

    public function StockFirst()
    {
        return $this->hasOne(Stock::class, 'produk_obat_id', 'id');
    }

    public function PengeluaranStockDetail()
    {
        return $this->hasMany(PengeluaranStockDetail::class, 'produk_obat_id', 'id');
    }

    public function MutasiStock()
    {
        return $this->hasManyThrough(
            MutasiStock::class,
            Stock::class,
            'produk_obat_id', // Foreign key on do table...
            'stock_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function KategoriObat()
    {
        return $this->belongsTo(KategoriObat::class);
    }
}

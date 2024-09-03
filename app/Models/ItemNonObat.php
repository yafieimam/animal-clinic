<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class ItemNonObat extends Model
{
    use Loggable;

    protected $table = "ms_item_non_obat";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'name',
        'satuan_non_obat_id',
        'kategori',
        'jenis',
        'harga',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function SatuanNonObat()
    {
        return $this->belongsTo(SatuanNonObat::class);
    }

    public function Satuan()
    {
        return $this->belongsTo(SatuanNonObat::class, 'satuan_non_obat_id', 'id');
    }

    public function Stock()
    {
        return $this->hasMany(Stock::class, 'item_non_obat_id', 'id');
    }

    public function StockFirst()
    {
        return $this->hasOne(Stock::class, 'item_non_obat_id', 'id');
    }

    public function PengeluaranStockDetail()
    {
        return $this->hasMany(PengeluaranStockDetail::class, 'item_non_obat_id', 'id');
    }

    public function MutasiStock()
    {
        return $this->hasManyThrough(
            MutasiStock::class,
            Stock::class,
            'item_non_obat_id', // Foreign key on do table...
            'stock_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }
}

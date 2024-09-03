<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class PengeluaranStockDetail extends Model
{
    use Loggable;
    use \Awobaz\Compoships\Compoships;

    protected $table = "ms_pengeluaran_stock_detail";
    protected $primaryKey = 'id';

    protected $fillable = [
        'pengeluaran_stock_id',
        'id',
        'jenis_stock',
        'produk_obat_id',
        'item_non_obat_id',
        'qty',
        'total_harga',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public static $enumJenisStock = [
        'OBAT',
        'NON OBAT',
    ];

    public function PengeluaranStock()
    {
        return $this->belongsTo(PengeluaranStock::class);
    }

    public function ProdukObat()
    {
        return $this->belongsTo(ProdukObat::class);
    }

    public function ItemNonObat()
    {
        return $this->belongsTo(ItemNonObat::class);
    }

    public function PengeluaranStockDetailMutasi()
    {
        return $this->hasMany(PengeluaranStockDetailMutasi::class, ['pengeluaran_stock_id', 'pengeluaran_stock_detail_id'], ['pengeluaran_stock_id', 'id']);
    }
}

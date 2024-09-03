<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class PengeluaranStockDetailMutasi extends Model
{
    use Loggable;
    use \Awobaz\Compoships\Compoships;

    protected $table = "ms_pengeluaran_stock_detail_mutasi";
    protected $primaryKey = 'id';

    protected $fillable = [
        'pengeluaran_stock_id',
        'pengeluaran_stock_detail_id',
        'id',
        'mutasi_stock_id',
        'harga_satuan',
        'qty',
        'total_harga',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function PengeluaranStock()
    {
        return $this->belongsTo(PengeluaranStock::class);
    }

    public function PengeluaranStockDetail()
    {
        return $this->belongsTo(PengeluaranStockDetail::class, ['pengeluaran_stock_id', 'pengeluaran_stock_detail_id'], ['pengeluaran_stock_id', 'id']);
    }

    public function ProdukObat()
    {
        return $this->belongsTo(ProdukObat::class);
    }

    public function MutasiStock()
    {
        return $this->hasOne(MutasiStock::class, 'id', 'mutasi_stock_id');
    }
}

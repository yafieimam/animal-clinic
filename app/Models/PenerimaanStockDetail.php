<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class PenerimaanStockDetail extends Model
{
    use Loggable;

    protected $table = "ms_penerimaan_stock_detail";
    protected $primaryKey = 'id';

    protected $fillable = [
        'penerimaan_stock_id',
        'id',
        'jenis_stock',
        'produk_obat_id',
        'item_non_obat_id',
        'harga_satuan',
        'qty',
        'total_harga',
        'mutasi_stock_id',
        'expired_date',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public static $enumJenisStock = [
        'OBAT',
        'NON OBAT',
    ];

    public function PenerimaanStock()
    {
        return $this->belongsTo(PenerimaanStock::class);
    }

    public function ProdukObat()
    {
        return $this->belongsTo(ProdukObat::class);
    }

    public function ItemNonObat()
    {
        return $this->belongsTo(ItemNonObat::class);
    }


    public function MutasiStock()
    {
        return $this->hasOne(MutasiStock::class, 'id', 'mutasi_stock_id');
    }
}

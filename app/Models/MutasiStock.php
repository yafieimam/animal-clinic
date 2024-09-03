<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class MutasiStock extends Model
{
    use Loggable;

    protected $table = "ms_mutasi_stock";
    protected $primaryKey = 'id';

    protected $fillable = [
        'stock_id',
        'id',
        'harga_satuan',
        'total_harga',
        'qty',
        'qty_tersisa',
        'referensi',
        'jenis',
        'expired_date',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d',
    ];


    public function Stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function PengeluaranStockDetailMutasi()
    {
        return $this->hasOne(PengeluaranStockDetailMutasi::class, 'mutasi_stock_id', 'id');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}

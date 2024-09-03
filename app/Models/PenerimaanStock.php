<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class PenerimaanStock extends Model
{
    use Loggable;

    protected $table = "ms_penerimaan_stock";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'branch_id',
        'supplier_id',
        'tanggal_terima',
        'file_faktur',
        'nomor_faktur',
        'description',
        'pengeluaran_stock_id',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function PenerimaanStockDetail()
    {
        return $this->hasMany(PenerimaanStockDetail::class);
    }

    public function Supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function PengeluaranStock()
    {
        return $this->belongsTo(PengeluaranStock::class, 'pengeluaran_stock_id', 'id');
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

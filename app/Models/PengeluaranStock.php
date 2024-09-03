<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class PengeluaranStock extends Model
{
    use Loggable;

    protected $table = "ms_pengeluaran_stock";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'jenis',
        'branch_id',
        'branch_tujuan_id',
        'tanggal_pengeluaran',
        'file_faktur',
        'nomor_faktur',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public static $enumJenis = [
        'PINDAH CABANG',
        // 'STOCK OPNAME',
    ];


    public function PenerimaanStock()
    {
        return $this->hasOne(PenerimaanStock::class, 'pengeluaran_stock_id', 'id');
    }

    public function PengeluaranStockDetail()
    {
        return $this->hasMany(PengeluaranStockDetail::class);
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function BranchTujuan()
    {
        return $this->belongsTo(Branch::class, 'branch_tujuan_id', 'id');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}

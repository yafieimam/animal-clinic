<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class SupplierDetail extends Model
{
    use Loggable;

    protected $table = "mo_supplier_detail";
    protected $primaryKey = 'id';

    protected $fillable = [
        'supplier_id',
        'id',
        'produk_obat_id',
        'harga_terakhir',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function Supplier()
    {
        return $this->belongsTo('\App\Supplier');
    }
}

<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class PermintaanStock extends Model
{
    use Loggable;

    protected $table = "ms_permintaan_stock";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'jenis_stock',
        'branch_id',
        'produk_obat_id',
        'item_non_obat_id',
        'qty',
        'status',
        'ref',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function ProdukObat()
    {
        return $this->belongsTo(ProdukObat::class);
    }

    public function ItemNonObat()
    {
        return $this->belongsTo(ItemNonObat::class);
    }
    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
    public function UpdatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}

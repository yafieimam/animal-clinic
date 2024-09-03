<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use Loggable;

    protected $table = "ms_supplier";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'branch_id',
        'name',
        'alamat',
        'telpon',
        'email',
        'npwp',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function SupplierDetail()
    {
        return $this->hasMany(SupplierDetail::class);
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

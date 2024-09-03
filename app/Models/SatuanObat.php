<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class SatuanObat extends Model
{
    use Loggable;
    protected $table = "mo_satuan_obat";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function ProdukObat()
    {
        return $this->hasMany(ProdukObat::class);
    }
}

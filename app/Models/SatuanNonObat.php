<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class SatuanNonObat extends Model
{
    use Loggable;

    protected $table = "ms_satuan_non_obat";
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

    public function ItemNonObat()
    {
        return $this->hasMany(ItemNonObat::class);
    }
}

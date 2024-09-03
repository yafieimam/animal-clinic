<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class Ras extends Model
{
    use Loggable;

    protected $table = "mk_ras";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'binatang_id',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function binatang()
    {
        return $this->belongsTo(Binatang::class);
    }
}

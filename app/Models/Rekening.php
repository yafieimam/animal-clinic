<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    use Loggable;

    protected $table = "mk_rekening";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'branch_id',
        'bank',
        'no_rekening',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

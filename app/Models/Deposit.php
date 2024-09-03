<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use Loggable;

    protected $table = "t_deposit";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'owner_id',
        'branch_id',
        'nilai_deposit',
        'sisa_deposit',
        'keterangan',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function Owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function DepositMutasi()
    {
        return $this->hasMany(DepositMutasi::class, 'deposit_id', 'id');
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

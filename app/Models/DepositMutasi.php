<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositMutasi extends Model
{
    use Loggable;

    protected $table = "t_deposit_mutasi";
    protected $primaryKey = 'id';

    protected $fillable = [
        'deposit_id',
        'id',
        'jenis_deposit',
        'nilai',
        'ref',
        'metode_pembayaran',
        'nama_bank',
        'nomor_kartu',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
        'atas_nama',
        'branch_id',
    ];

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function UpdatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    public function kasir()
    {
        return $this->belongsTo(Kasir::class, 'ref', 'kode');
    }
}

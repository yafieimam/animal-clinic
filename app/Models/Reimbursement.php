<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    use Loggable;

    protected $table = "t_reimbursement";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'nama_klaim',
        'tanggal',
        'jumlah_biaya',
        'tipe_klaim',
        'status',
        'keterangan',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'keterangan_approval'
    ];

    public static $enumTipeKlaim = [
        'Medical Claim',
        'Overtime',
        'Visit',
        'Antar Jemput'
    ];


    public function ReimbursementFileApproval()
    {
        return $this->hasMany(ReimbursementFileApproval::class);
    }

    public function ReimbursementFileKlaim()
    {
        return $this->hasMany(ReimbursementFileKlaim::class);
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

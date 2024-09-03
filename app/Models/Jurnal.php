<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use Loggable;

    protected $table = "t_jurnal";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'branch_id',
        'tanggal',
        'metode_pembayaran',
        'nama_bank',
        'nomor_kartu',
        'ref',
        'jenis',
        'dk',
        'jenis_transaksi',
        'description',
        'nominal',
        'status',
        'approved_by',
        'alasan',
        'attachment',
        'ref_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function JurnalDetail()
    {
        return $this->hasMany(JurnalDetail::class);
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ApprovedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function UpdatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}

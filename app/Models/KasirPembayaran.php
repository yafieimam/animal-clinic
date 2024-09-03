<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasirPembayaran extends Model
{

    use Loggable;

    protected $table = "t_kasir_pembayaran";
    protected $primaryKey =  'kasir_id';

    protected $fillable = [
        'kasir_id',
        'id',
        'ref',
        'nilai_pembayaran',
        'diskon_cicilan',
        'jenis_pembayaran',
        'nama_bank',
        'nomor_kartu',
        'nomor_transaksi',
        'keterangan',
        'bukti_transfer',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function Kasir()
    {
        return $this->belongsTo(Kasir::class);
    }

    public function Deposit()
    {
        return $this->belongsTo(Deposit::class, 'ref', 'kode');
    }


    public function ItemNonObat()
    {
        return $this->belongsTo(ItemNonObat::class, 'ref', 'id');
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

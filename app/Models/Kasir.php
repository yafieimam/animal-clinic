<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Kasir extends Model
{
    use Loggable;

    protected $table = "t_kasir";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'branch_id',
        'tanggal',
        'nama_owner',
        'langsung_lunas',
        'total_item_diskon',
        'total_item_non_diskon',
        'total_obat',
        'total_lain',
        'diskon_penyesuaian',
        'total_bayar',
        'diskon',
        'pembayaran',
        'diterima',
        'uang_kembali',
        'metode_pembayaran',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deposit',
        'nama_bank',
        'nomor_kartu',
        'nomor_transaksi',
        'tarik_deposit',
        'kode_deposit',
        'owner_id',
        'email',
        'type_kasir',
        'penarikan_deposit',
        'sisa_pelunasan',
        'catatan_kasir',
        'bukti_transfer',
        'nominal_transfer'
    ];

    public static $enumTypeKasir = [
        'Normal',
        'Rescue an Clow',
        'Rescue an MMI',
    ];


    public function KasirDetail()
    {
        return $this->hasMany(KasirDetail::class);
    }

    public function KasirPembayaran()
    {
        return $this->hasMany(KasirPembayaran::class);
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function Owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

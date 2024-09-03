<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    use Loggable;

    protected $table = "t_jurnal_detail";
    protected $primaryKey = 'jurnal_id';

    protected $fillable = [
        'jurnal_id',
        'id',
        'master_akun_transaksi_id',
        'redaksi',
        'harga',
        'qty',
        'sub_total',
        'pasien_id',
        'status_deposit',
        'proofment',
        'created_at',
        'updated_at'
    ];

    public function Jurnal()
    {
        return $this->belongsTo(Jurnal::class);
    }

    public function Pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function MasterAkunTransaksi()
    {
        return $this->belongsTo(MasterAkunTransaksi::class);
    }
}

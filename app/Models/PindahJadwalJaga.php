<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class PindahJadwalJaga extends Model
{
    use Loggable;

    protected $table = "mka_pindah_jadwal_jaga";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'tanggal_awal',
        'tanggal_tujuan',
        'branch_id',
        'dokter_peminta',
        'dokter_diminta',
        'jadwal_dokter_awal_id',
        'jadwal_dokter_tujuan_id',
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

    public function Poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function DokterPeminta()
    {
        return $this->belongsTo(User::class, 'dokter_peminta', 'id');
    }

    public function DokterDiminta()
    {
        return $this->belongsTo(User::class, 'dokter_diminta', 'id');
    }

    public function JadwalDokterAwal()
    {
        return $this->belongsTo(JadwalDokter::class, 'jadwal_dokter_awal_id', 'id');
    }

    public function JadwalDokterTujuan()
    {
        return $this->belongsTo(JadwalDokter::class, 'jadwal_dokter_tujuan_id', 'id');
    }
}

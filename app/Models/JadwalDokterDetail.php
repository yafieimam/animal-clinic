<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class JadwalDokterDetail extends Model
{
    use Loggable;

    protected $table = "mka_jadwal_dokter_detail";
    protected $primaryKey = 'jadwal_dokter_id';

    protected $fillable = [
        'jadwal_dokter_id',
        'id',
        'dokter',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function JadwalDokter()
    {
        return $this->belongsTo(JadwalDokter::class);
    }

    public function DataDokter()
    {
        return $this->belongsTo(User::class, 'dokter', 'id');
    }

    public function DokterPeminta()
    {
        return $this->belongsTo(PindahJadwalJaga::class, 'DokterPeminta', 'DokterPeminta');
    }

    public function DokterDiminta()
    {
        return $this->belongsTo(PindahJadwalJaga::class, 'DokterDiminta', 'DokterDiminta');
    }
}

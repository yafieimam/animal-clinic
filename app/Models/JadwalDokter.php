<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    use Loggable;

    protected $table = "mka_jadwal_dokter";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'branch_id',
        'poli_id',
        'jam_pertama_id',
        'jam_terakhir_id',
        'hari',
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

    public function JadwalDokterDetail()
    {
        return $this->hasMany(JadwalDokterDetail::class);
    }

    public function JamPertama()
    {
        return $this->belongsTo(JamKerja::class, 'jam_pertama_id', 'id');
    }

    public function JamTerakhir()
    {
        return $this->belongsTo(JamKerja::class, 'jam_terakhir_id', 'id');
    }

    public function PindahJadwalJagaAwal()
    {
        return $this->hasMany(PindahJadwalJaga::class, 'jadwal_dokter_awal_id', 'id');
    }

    public function PindahJadwalJagaTujuan()
    {
        return $this->hasMany(PindahJadwalJaga::class, 'jadwal_dokter_tujuan_id', 'id');
    }
}

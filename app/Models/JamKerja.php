<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    use Loggable;

    protected $table = "mka_jam_kerja";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'sequence',
        'jam_awal',
        'menit_awal',
        'jam_akhir',
        'menit_akhir',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function JamPertama()
    {
        return $this->hasMany(JadwalDokter::class, 'jam_pertama_id', 'id');
    }

    public function JamTerakhir()
    {
        return $this->hasMany(JadwalDokter::class, 'jam_terakhir_id', 'id');
    }

    public function Dokter()
    {
        return $this->belongsTo(User::class, 'dokter', 'id');
    }
}

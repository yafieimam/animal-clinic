<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class Pendaftaran extends Model
{
    use Loggable;

    protected $table = "qm_pendaftaran";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode_pendaftaran',
        'tanggal',
        'owner_id',
        'branch_id',
        'catatan',
        'dokter',
        'status',
        'status_apotek',
        'status_pickup',
        'status_owner',
        'request_dokter',
        'poli_id',
        'status_pembayaran_penjemputan',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'jam_pickup',
    ];

    public function Dokter()
    {
        return $this->belongsTo(User::class, 'dokter', 'id');
    }

    public function requestDokter()
    {
        return $this->belongsTo(User::class, 'request_dokter', 'id');
    }

    public function Pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function PendaftaranPasien()
    {
        return $this->hasMany(PendaftaranPasien::class, 'pendaftaran_id', 'id');
    }


    public function PendaftaranPasienAnamnesa()
    {
        return $this->hasMany(PendaftaranPasienAnamnesa::class, 'pendaftaran_id', 'id');
    }

    public function RekamMedisPasien()
    {
        return $this->hasMany(RekamMedisPasien::class, 'pendaftaran_id', 'id');
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function Owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function Poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function RekamMedisLogHistory()
    {
        return $this->hasManyThrough(
            RekamMedisLogHistory::class,
            RekamMedisPasien::class,
            'pendaftaran_id', // Foreign key on do table...
            'rekam_medis_pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function RekamMedisDiagnosa()
    {
        return $this->hasManyThrough(
            RekamMedisDiagnosa::class,
            RekamMedisPasien::class,
            'pendaftaran_id', // Foreign key on do table...
            'rekam_medis_pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function RekamMedisTindakan()
    {
        return $this->hasManyThrough(
            RekamMedisTindakan::class,
            RekamMedisPasien::class,
            'pendaftaran_id', // Foreign key on do table...
            'rekam_medis_pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function RekamMedisTreatment()
    {
        return $this->hasManyThrough(
            RekamMedisTreatment::class,
            RekamMedisPasien::class,
            'pendaftaran_id', // Foreign key on do table...
            'rekam_medis_pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function RekamMedisHasilLab()
    {
        return $this->hasManyThrough(
            RekamMedisHasilLab::class,
            RekamMedisPasien::class,
            'pendaftaran_id', // Foreign key on do table...
            'rekam_medis_pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function RekamMedisCatatan()
    {
        return $this->hasManyThrough(
            RekamMedisCatatan::class,
            RekamMedisPasien::class,
            'pendaftaran_id', // Foreign key on do table...
            'rekam_medis_pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function RekamMedisResep()
    {
        return $this->hasManyThrough(
            RekamMedisResep::class,
            RekamMedisPasien::class,
            'pendaftaran_id', // Foreign key on do table...
            'rekam_medis_pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }
}

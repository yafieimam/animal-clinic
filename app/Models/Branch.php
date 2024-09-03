<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use Loggable;

    protected $table = "mk_branch";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'lokasi',
        'alamat',
        'lat',
        'long',
        'telpon',
        'open_time',
        'close_time',
        'open_holiday_time',
        'close_holiday_time',
        'branch_supervisor',
        'hari_libur',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function PermintaanStock()
    {
        return $this->hasMany(PermintaanStock::class);
    }

    public function Kasir()
    {
        return $this->hasMany(Kasir::class);
    }

    public function Jurnal()
    {
        return $this->hasMany(Jurnal::class);
    }

    public function Pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function PendaftaranPasien()
    {
        return $this->hasManyThrough(
            PendaftaranPasien::class,
            Pendaftaran::class,
            'branch_id', // Foreign key on do table...
            'pendaftaran_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }


    public function RekamMedisPasien()
    {
        return $this->hasManyThrough(
            RekamMedisPasien::class,
            Pendaftaran::class,
            'branch_id', // Foreign key on do table...
            'pendaftaran_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function depositMutasi()
    {
        return $this->hasMany(
            DepositMutasi::class,
            'branch_id', // Foreign key on do table...
            'id' // Local key on users table...
        );
    }
}

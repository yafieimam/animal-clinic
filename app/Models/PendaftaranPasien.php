<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranPasien extends Model
{
    use HasFactory;

    use Loggable;

    protected $table = "qm_pendaftaran_pasien";
    protected $primaryKey = 'id';

    protected $fillable = [
        'pendaftaran_id',
        'id',
        'pasien_id',
        'lain_lain',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'dokter_periksa',
    ];

    public function Pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function Pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function Dokter()
    {
        return $this->belongsTo(User::class, 'dokter_periksa', 'id');
    }

    public function UpdatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}

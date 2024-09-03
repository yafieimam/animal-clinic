<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranPasienAnamnesa extends Model
{
    use HasFactory;
    protected $table = "qm_pendaftaran_pasien_anamnesa";
    protected $primaryKey = 'id';

    protected $fillable = [
        'pendaftaran_id',
        'id',
        'pasien_id',
        'anamnesa_id',
        'ya',
        'tidak',
        'keterangan',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];


    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function anamnesa()
    {
        return $this->belongsTo(Anamnesa::class);
    }
}

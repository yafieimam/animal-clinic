<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pasien extends Model
{
    use Loggable;

    protected $table = "mp_pasien";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'name',
        'binatang_id',
        'ras_id',
        'owner_id',
        'branch_id',
        'ciri_khas',
        'date_of_birth',
        'life_stage',
        'berat',
        'tinggi',
        'suhu',
        'sex',
        'image',
        'tanggal_awal_periksa',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
    ];


    public static $enumJenisKelamin = [
        'JANTAN',
        'BETINA',
    ];

    public static $enumLifeStage = [
        [
            'title' => 'Kitten/Puppy',
            'description' => '< 1 Th',
            'min' => '1',
            'max' => '0',
            'operator' => '<',
        ],
        [
            'title' => 'Adult',
            'description' => '1 - 7 Th',
            'min' => '1',
            'max' => '7',
            'operator' => '<=',
        ],
        [
            'title' => 'Geriatric',
            'description' => '> 7 Th',
            'min' => '0',
            'max' => '7',
            'operator' => '>',
        ],
    ];

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function Owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id');
    }

    public function binatang()
    {
        return $this->belongsTo(Binatang::class);
    }

    public function pasienMeninggal()
    {
        return $this->hasOne(PasienMeninggal::class);
    }

    public function Ras()
    {
        return $this->belongsTo(Ras::class);
    }

    public function RekamMedisPasien()
    {
        return $this->hasMany(RekamMedisPasien::class);
    }


    public function singleRekamMedisPasien()
    {
        return $this->hasOne(RekamMedisPasien::class);
    }

    public function Pendaftaran()
    {
        return $this->hasManyThrough(
            Pendaftaran::class,
            PendaftaranPasien::class,
            'pasien_id', // Foreign key on do table...
            'qm_pendaftaran.id', // Foreign key on bpk table...
            'mp_pasien.id', // Local key on marketing table...
            'pendaftaran_id' // Local key on users table...
        );
    }

    public function PendaftaranPasien()
    {
        return $this->hasMany(PendaftaranPasien::class);
    }

    public function PendaftaranPasienAnamnesa()
    {
        return $this->hasMany(PendaftaranPasienAnamnesa::class, 'pasien_id', 'id');
    }

    public function RekamMedisLogHistory()
    {
        return $this->hasManyThrough(
            RekamMedisLogHistory::class,
            RekamMedisPasien::class,
            'pasien_id', // Foreign key on do table...
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
            'pasien_id', // Foreign key on do table...
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
            'pasien_id', // Foreign key on do table...
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
            'pasien_id', // Foreign key on do table...
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
            'pasien_id', // Foreign key on do table...
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
            'pasien_id', // Foreign key on do table...
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
            'pasien_id', // Foreign key on do table...
            'rekam_medis_pasien_id', // Foreign key on bpk table...
            'id', // Local key on marketing table...
            'id' // Local key on users table...
        );
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function UpdatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}

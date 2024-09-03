<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class RekamMedisPasien extends Model
{
    use Loggable;
    protected $table = "mp_rekam_medis_pasien";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'pasien_id',
        'berat',
        'suhu',
        'gejala',
        'pendaftaran_id',
        'tindakan_bedah',
        'rawat_inap',
        'rawat_jalan',
        'bius',
        'grooming',
        'titip_sehat',
        'status',
        'status_bedah',
        'status_urgent',
        'status_pemeriksaan',
        'status_pengambilan_obat',
        'status_pembayaran',
        'status_kepulangan',
        'catatan',
        'hasil_pemeriksaan',
        'rekomendasi_tindakan_bedah',
        'pakan',
        'jenis_grooming',
        'cukur',
        'rekomendasi_tanggal_bedah',
        'anamnesa',
        'diagnosa',
        'upload_form_persetujuan',
        'alasan_pulang_paksa',
        'upload_pulang_paksa',
        'kembali_ke_apotek',
        'created_by',
        'updated_by',
        'created_at',
        'tanggal_keluar',
        'updated_at',

        // start add 16-Jan-2023
        'status_apoteker',
        'progress_by',
        //end
    ];
    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d H:i',
    // ];

    protected $appends = ['mp_owner_name', 'mp_pasien_name', 'dokter_name'];

    public function getMpOwnerNameAttribute()
    {
        return $this->Pasien ? ($this->Pasien->Owner ? $this->Pasien->Owner->name : $this->Pasien->name) : $this->pasien_id;
    }

    public function getMpPasienNameAttribute()
    {
        return $this->Pasien ? $this->Pasien->name : $this->pasien_id;
    }

    public function getDokterNameAttribute()
    {
        return $this->CreatedBy ? $this->CreatedBy->name : $this->created_by;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i');
    }

    public function Pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id', 'id');
    }

    public function PasienMeninggal()
    {
        return $this->hasOne(PasienMeninggal::class);
    }

    public function Pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function UpdatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function Kasir()
    {
        return $this->hasOne(Kasir::class, 'rekam_medis_pasien_id', 'id');
    }

    public function KamarRawatInapDanBedahDetail()
    {
        return $this->hasMany(KamarRawatInapDanBedahDetail::class);
    }

    public function KamarRawatInapDanBedahDetailFirst()
    {
        return $this->hasOne(KamarRawatInapDanBedahDetail::class, 'rekam_medis_pasien_id', 'id');
    }

    public function JenisGrooming()
    {
        return $this->belongsTo(Tindakan::class, 'jenis_grooming', 'id');
    }

    public function RekamMedisDiagnosa()
    {
        return $this->hasMany(RekamMedisDiagnosa::class);
    }

    public function RekamMedisTreatment()
    {
        return $this->hasMany(RekamMedisTreatment::class);
    }

    public function RekamMedisTindakan()
    {
        return $this->hasMany(RekamMedisTindakan::class);
    }

    public function RekamMedisCatatan()
    {
        return $this->hasMany(RekamMedisCatatan::class);
    }

    public function RekamMedisKondisiHarian()
    {
        return $this->hasMany(RekamMedisKondisiHarian::class);
    }

    public function RekamMedisResep()
    {
        return $this->hasMany(RekamMedisResep::class);
    }

    public function SingleRekamMedisResep()
    {
        return $this->hasOne(RekamMedisResep::class);
    }

    public function RekamMedisResepRacikan()
    {
        return $this->hasMany(RekamMedisResep::class);
    }

    public function RekamMedisPakan()
    {
        return $this->hasMany(RekamMedisPakan::class);
    }

    public function RekamMedisNonObat()
    {
        return $this->hasMany(RekamMedisNonObat::class, 'rekam_medis_pasien_id', 'id');
    }

    public function RekamMedisHasilLab()
    {
        return $this->hasMany(RekamMedisHasilLab::class);
    }

    public function RekamMedisRekomendasiTindakanBedah()
    {
        return $this->hasMany(RekamMedisRekomendasiTindakanBedah::class, 'rekam_medis_pasien_id', 'id');
    }

    public function RekamMedisLogHistory()
    {
        return $this->hasMany(RekamMedisLogHistory::class, 'rekam_medis_pasien_id', 'id');
    }
}

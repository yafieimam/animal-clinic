<?php

namespace App\Models;

use App\Models\Traits\ResepTrait;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class RekamMedisResep extends Model
{
    use Loggable;
    use ResepTrait;

    use \Awobaz\Compoships\Compoships;
    protected $table = "mp_rekam_medis_resep";
    protected $primaryKey = 'rekam_medis_pasien_id';

    protected $fillable = [
        'rekam_medis_pasien_id',
        'id',
        'produk_obat_id',
        'jenis_obat',
        'kategori_obat_id',
        'satuan_obat_id',
        'qty',
        'dokter',
        'harga_jual',
        'status_resep',
        'status_pembuatan_obat',
        'status_pembayaran_obat',
        'harga_jual',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function RekamMedisPasien()
    {
        return $this->belongsTo(RekamMedisPasien::class);
    }

    public function ProdukObat()
    {
        return $this->belongsTo(ProdukObat::class);
    }

    public function SatuanObat()
    {
        return $this->belongsTo(SatuanObat::class);
    }

    public function KategoriObat()
    {
        return $this->belongsTo(KategoriObat::class);
    }

    public function RekamMedisResepRacikan()
    {
        return $this->hasMany(RekamMedisResepRacikan::class, ['rekam_medis_pasien_id', 'rekam_medis_resep_id'], ['rekam_medis_pasien_id', 'id']);
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter', 'id');
    }

    public function UpdatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}

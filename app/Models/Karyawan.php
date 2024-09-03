<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use Loggable;

    protected $table = "mkr_karyawan";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'nama_panggilan',
        'branch_id',
        'divisi_id',
        'jabatan_id',
        'nik',
        'email',
        'telpon',
        'status_pernikahan',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'jenis_kelamin',
        'rt',
        'rw',
        'kode_pos',
        'alamat',
        'npwp',
        'bpjs',
        'tanggal_join',
        'tanggal_lahir',
        'tempat_lahir',
        'bagian_id',
        'jumlah_anak',
        'file_ktp',
    ];

    protected $casts = [
        'province_id' => 'string',
        'city_id' => 'string',
        'district_id' => 'string',
        'village_id' => 'string',
    ];

    public static $enumStatusPernikahan = [
        'Menikah',
        'Belum Menikah',
        'Duda/Janda',
        'Cerai'
    ];

    public static $enumJenisKelamin = [
        'LAKI',
        'PEREMPUAN',
    ];

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function Provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'province_id', 'id');
    }

    public function Kota()
    {
        return $this->belongsTo(Kota::class, 'city_id', 'id');
    }

    public function Kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'district_id', 'id');
    }

    public function Kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'village_id', 'id');
    }

    public function User()
    {
        return $this->hasMany(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class Owner extends Model
{
    use Loggable;

    protected $table = "mp_owner";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'kode',
        'name',
        'branch_id',
        'nik',
        'email',
        'telpon',
        'alamat',
        'komunitas',
        'catatan',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function Deposit()
    {
        return $this->hasOne(Deposit::class);
    }

    public function Pasien()
    {
        return $this->hasMany(Pasien::class);
    }

    public function singlePasien()
    {
        return $this->hasOne(Pasien::class);
    }

    public function CreatedBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function UpdatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function singleRekamMedisPasien()
    {
        return $this->hasOneThrough(
            RekamMedisPasien::class,
            Pasien::class,
        );
    }
}

<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anamnesa extends Model
{
    use HasFactory;

    use Loggable;

    protected $table = "mk_anamnesa";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'poli_id',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function pendaftaranPasienAnamnesa()
    {
        return $this->hasMany(PendaftaranPasienAnamnesa::class);
    }
}

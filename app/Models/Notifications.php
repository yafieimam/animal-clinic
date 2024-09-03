<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use Loggable;

    protected $table = "notifications";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'status',
        'approved_by',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'id' => 'string',
    ];


    public function CreatedBy()
    {
        return $this->belongsTo('\App\User', 'approved_by', 'id');
    }
}

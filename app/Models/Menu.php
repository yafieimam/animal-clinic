<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use Loggable;

    protected $table = "s_menu";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'description',
        'group_menu_id',
        'url',
        'type',
        'sequence',
        'icon',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function GroupMenu()
    {
        return $this->belongsTo(GroupMenu::class);
    }

    public function hakAkses()
    {
        return $this->hasMany(HakAkses::class);
    }
}

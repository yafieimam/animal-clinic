<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class TitleMenu extends Model
{
    use Loggable;

    protected $table = "s_title_menu";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'sequence',
        'description',
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
        return $this->hasMany(GroupMenu::class);
    }
}

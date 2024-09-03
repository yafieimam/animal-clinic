<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class GroupMenu extends Model
{
    use Loggable;

    protected $table = "s_group_menu";
    protected $primaryKey = 'id';
    public $loggable_actions = ['edit', 'create', 'delete'];
    protected $fillable = [
        'id',
        'name',
        'description',
        'slug',
        'icon',
        'url',
        'sequence',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'title_menu_id',
        'type',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function Menu()
    {
        return $this->hasMany(Menu::class);
    }

    public function TitleMenu()
    {
        return $this->belongsTo(TitleMenu::class);
    }
}

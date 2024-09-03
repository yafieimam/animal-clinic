<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class KnowledgeSharingFile extends Model
{
    use Loggable;

    protected $table = "s_knowledge_sharing_file";
    protected $primaryKey = 'id';

    protected $fillable = [
        'knowledge_sharing_id',
        'id',
        'file',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];


}

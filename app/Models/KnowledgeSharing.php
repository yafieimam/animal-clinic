<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class KnowledgeSharing extends Model
{
    use Loggable;

    protected $table = "s_knowledge_sharing";
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'title',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function KnowledgeSharingFile()
    {
        return $this->hasMany(KnowledgeSharingFile::class);
    }
}

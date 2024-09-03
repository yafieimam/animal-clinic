<?php

namespace App\Models;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class ReimbursementFileApproval extends Model
{
    use Loggable;

    protected $table = "t_reimbursement_file_approval";
    protected $primaryKey = 'id';

    protected $fillable = [
        'reimbursement_id',
        'id',
        'file',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function Reimbursement()
    {
        return $this->belongsTo(Reimbursement::class);
    }
}

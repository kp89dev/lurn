<?php
namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWorkflow extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $dates = [
        'deleted_at',
        'create_at',
        'updated_at',
        'next_step_time'
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

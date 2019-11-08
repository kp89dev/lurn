<?php
namespace App\Models\Workflows;

use App\Models\EmailStatus;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'goal'     => 'array',
        'enroll'   => 'array',
        'workflow' => 'array'
    ];
    
    public function stats()
    {
        return $this->hasMany(WorkflowStatistic::class);
    }

    public function emailStats()
    {
        return $this->hasMany(EmailStatus::class);
    }
    
    public function statSummary()
    {
        return $this->stats()
            ->where('period', '30 day')
            ->orderBy('created_at', 'desc')
            ->first();
    }
}

<?php

namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Model;

class WorkflowStatistic extends Model
{
    protected $fillable = ['workflow_id', 'step', 'period', 'send', 'delivery', 'bounce', 'open', 'click'];
    
    public function workflow() {
        return $this->belongsTo(Workflow::class);
    }
    
    /**
     * Gets an array of the percentages rather than the 
     * @return boolean|number[]
     */
    public function getPercents()
    {
        if($this->send == 0) {
            return false;
        }
        return [
            'send'      => 1,
            'delivery'  => $this->delivery / $this->send,
            'bounce'    => $this->bounce / $this->send,
            'open'      => $this->open / $this->send,
            'click'     => $this->click / $this->send,
        ];
    }
    
    public function scopeSummary($query)
    {
        return $query->where('period', '=', '30 DAY')
            ->orderBy('created_at', 'DESC');
    }
}

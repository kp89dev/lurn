<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = ['all_day' => 'boolean'];
    
    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];
    
    public function container()
    {
        return $this->belongsTo(CourseContainer::class, 'course_container_id', 'id');
    }

    public function getStartAttribute()
    {
        return $this->all_day ?
        new Carbon($this->start_date->format('Y-m-d '))
        : new Carbon($this->start_date->format('Y-m-d ') . $this->start_time, 'America/New_York');
    }

    public function getEndAttribute()
    {
        return $this->all_day ?
            new Carbon($this->end_date->format('Y-m-d '))
            : new Carbon($this->end_date->format('Y-m-d ') . $this->end_time, 'America/New_York');
    }
}

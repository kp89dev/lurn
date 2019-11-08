<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTool extends Model
{
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getSlugAttribute()
    {
        return str_slug($this->tool_name);
    }
}

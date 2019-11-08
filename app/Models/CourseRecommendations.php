<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRecommendations extends Model
{
    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'recommended_course_id');
    }
}

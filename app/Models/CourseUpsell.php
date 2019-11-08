<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method activeForCourse(Course $course)
 */
class CourseUpsell extends Model
{
    protected $guarded = [];

    public function infusionsoft()
    {
       return $this->belongsTo(CourseInfusionsoft::class, 'course_infusionsoft_id')
                   ->where('course_infusionsoft.upsell', '=', 1)
                   ->withDefault(function () {
                        return new CourseInfusionsoft();
                   });
    }

    public function succeedingCourse()
    {
        return $this->hasOne(Course::class, 'id', 'succeeds_course_id');
    }

    /**
     * @param Builder $query
     * @param Course  $course
     *
     * @return mixed
     */
    public function scopeActiveForCourse($query, Course $course)
    {
        return $query->where('succeeds_course_id', $course->id)
                     ->where('status', 1);
    }
}

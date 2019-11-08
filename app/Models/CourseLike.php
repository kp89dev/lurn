<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLike extends Model
{
    protected $guarded = ['id'];

    /**
     * Get the course this like is associated with.
     * 
     * @return \App\Models\Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user this like is associated with.
     * 
     * @return \App\Models\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to a particular course.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param integer $courseId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }
}

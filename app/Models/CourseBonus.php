<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBonus extends Model
{
    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    public function bonusCourse()
    {
        return $this->hasOne(Course::class, 'id', 'bonus_course_id');
    }
}

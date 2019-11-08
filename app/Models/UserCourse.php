<?php
namespace App\Models;

use App\Events\Course\CourseCompleted;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markCourseAsCompleted()
    {
        $this->completed_at = Carbon::now()->toDateTimeString();
        $this->status = 1;
        $this->save();

        event(new CourseCompleted($this->user, $this->course));
    }
}

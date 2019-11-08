<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonSubscriptions extends Model
{
    protected $guarded = ['id'];
    
    public function lessons()
    {
        return $this->belongsTo(Lesson::class);
    }
}

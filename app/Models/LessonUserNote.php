<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonUserNote extends Model
{
    protected $guarded = ['id'];

    protected $with = [
        'course',
        'lesson',
    ];

    public function getUnsafeNotesAttribute()
    {
        return $this->attributes['notes'];
    }

    public function getNotesAttribute()
    {
        $notes = $this->attributes['notes'] ?? '';
        $notes = htmlentities($notes, ENT_QUOTES, 'utf-8');
        $notes = preg_replace('/[\n]/', '<br>', $notes);

        return $notes;
    }

    public function getLessonIndexAttribute()
    {
        return $this->lesson->getIndex();
    }

    public function getModuleIndexAttribute()
    {
        return $this->lesson->module->getIndex();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}

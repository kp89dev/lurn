<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $guarded = ['id'];
    protected $appends = array('courseCount', 'bonusCount');

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_categories');
    }

    public function user()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    
    public function getCourseCountAttribute()
    {
        return $this->courses()->whereNotIn('course_id', CourseBonus::all()->pluck('bonus_course_id'))->count();
    }
    
    public function getBonusCountAttribute()
    {
        return $this->courses()->whereIn('course_id', CourseBonus::all()->pluck('bonus_course_id'))->count();
    }

    public function getPrintableImageUrl(): string
    {
        $file = "categories/$this->id/$this->thumbnail";
        $disk = Storage::disk('static');
        $cdnUrl = sprintf('%s/%s', config('app.cdn_static'), $file);

        // Check the file availability only locally or in the admin panel.
        if (! app()->environment('production')) {
            return $disk->exists($file) ? $cdnUrl : asset('images/onboarding/ob-default.png');
        }

        return $cdnUrl;
    }
}

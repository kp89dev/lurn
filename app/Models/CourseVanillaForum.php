<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseVanillaForum extends Model
{
    protected $fillable = ['client_id', 'client_secret', 'url', 'forum_rules'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

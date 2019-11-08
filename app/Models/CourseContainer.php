<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseContainer extends Model
{
    protected $guarded = ['id'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

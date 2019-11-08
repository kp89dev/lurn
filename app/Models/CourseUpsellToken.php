<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CourseUpsellToken extends Model
{
    protected $guarded = [];

    public function courseUpsell()
    {
       return $this->belongsTo(CourseUpsell::class, 'course_upsell_id')
                   ->withDefault(function () {
                        return new CourseUpsell();
                   });
    }

    public function generateNew(CourseUpsell $courseUpsell)
    {
        return $this->create([
            'course_upsell_id' => $courseUpsell->id,
            'token'            => bin2hex(random_bytes(10)),
            'used'             => 0
        ]);
    }
}

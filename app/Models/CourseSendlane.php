<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSendlane extends Model
{
    protected $table = 'course_sendlane';

    protected $guarded = ['id'];

    public function sendlaneCredentials()
    {
        return $this->belongsTo(Sendlane::class, 'sendlane_id');
    }
}

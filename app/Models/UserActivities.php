<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserSetting;

class UserActivities extends Model
{
    const COURSE_FINISHED = 1;
    const COURSE_BOUGHT   = 2;
    const GOT_CERTIFIED   = 3;

    protected $guarded = ['id'];
    protected $dates = [
        'created_at',
        'updated_at',
        'activity_time'
    ];
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function activeUser()
    {
        return $this->belongsTo(User::class);
    }
}

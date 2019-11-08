<?php
namespace App\Listeners\Account\Normal;

use App\Events\User\UserMerged;
use Illuminate\Support\Facades\DB;

class AdjustLessonSubscription
{
    public function handle(UserMerged $event)
    {
        DB::table('lesson_subscriptions')
            ->where('user_id', $event->userMerged->id)
            ->update(['user_id' => $event->user->id]);
    }
}

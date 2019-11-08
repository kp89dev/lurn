<?php
namespace App\Listeners\Account\Normal;

use App\Events\User\UserMerged;
use Illuminate\Support\Facades\DB;

class AdjustTestUsers
{
    public function handle(UserMerged $event)
    {
        DB::table('test_users')
            ->where('user_id', $event->userMerged->id)
            ->update(['user_id' => $event->user->id]);
    }
}

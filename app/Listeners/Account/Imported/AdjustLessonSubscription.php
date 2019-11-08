<?php
namespace App\Listeners\Account\Imported;

use App\Events\User\ImportedUserMerged;
use Illuminate\Support\Facades\DB;

class AdjustLessonSubscription
{
    public function handle(ImportedUserMerged $event)
    {
        DB::table('lesson_subscriptions')
            ->where('user_id', $event->importedUser->user_id)
            ->where('from_table', $event->importedUser->connection)
            ->update([
                'user_id' => $event->user->id,
                'from_table' => ''
            ]);
    }
}

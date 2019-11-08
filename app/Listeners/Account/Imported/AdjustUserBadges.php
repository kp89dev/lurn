<?php
namespace App\Listeners\Account\Imported;

use App\Events\User\ImportedUserMerged;
use Illuminate\Support\Facades\DB;

class AdjustUserBadges
{
    public function handle(ImportedUserMerged $event)
    {
        $badgeIds = DB::table('cml_id_map')
                ->join('user_badges_import_all', 'cml_id_map.old_id', '=', 'user_badges_import_all.badge_id')
                ->join('users_import_all', 'users_import_all.user_id', '=', 'user_badges_import_all.user_id')
                ->where('cml_id_map.connection', $event->importedUser->connection)
                ->select('cml_id_map.new_id')
                ->where('user_badges_import_all.user_id', $event->importedUser->user_id)
                ->where('user_badges_import_all.connection', $event->importedUser->connection)
                ->where('users_import_all.connection', $event->importedUser->connection)
                ->where('cml_id_map.type', 'badges')
                ->get();
        
        foreach ($badgeIds as $id) {
            DB::table('user_badges')->insert([
                'badge_id' => $id->new_id,
                'user_id'  => $event->user->id
            ]);
        }
    }
}

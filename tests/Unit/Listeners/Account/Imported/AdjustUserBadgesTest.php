<?php
namespace Tests\Unit\Listerners\Account\Imported;

use App\Events\User\ImportedUserMerged;
use App\Listeners\Account\Imported\AdjustUserBadges;
use App\Models\ImportedUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\TestResult;
use App\Models\User;
use App\Listeners\Account\Imported\AdjustTestUsers;
use Illuminate\Support\Facades\DB;

class AdjustUserBadgesTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function handle_successfully_moves_the_imported_user_badges()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);

        //generate 1 users to merge
        $userToMerge = factory(ImportedUser::class)->create([
            'email'      => 'imported@import.com',
            'connection' => 'inbox'
        ]);

        //create fake imported badges gained
        DB::table('user_badges_import_all')->insert([
            [
                'user_id'    => $userToMerge->user_id,
                'badge_id'   => 10,
                'comment'    => 'test_comment',
                'connection' => 'inbox'
            ], [
                'user_id'    => $userToMerge->user_id,
                'badge_id'   => 11,
                'comment'    => 'test comment',
                'connection' => 'inbox'
            ]
        ]);

        //create fake imported badges
        DB::table('cml_id_map')->insert([
            [
                'old_id'     => 10,
                'new_id'     => 100,
                'type'       => 'badges',
                'connection' => 'inbox'
            ], [
                'old_id'     => 11,
                'new_id'     => 101,
                'type'       => 'badges',
                'connection' => 'inbox'
            ]
        ]);
        
        $event    = new ImportedUserMerged($user, $userToMerge);
        $listener = new AdjustUserBadges();
        $listener->handle($event);

        $this->assertDatabaseHas('user_badges', [
            'user_id'  => $user->id,
            'badge_id' => 100,
        ]);
        $this->assertDatabaseHas('user_badges', [
            'user_id'  => $user->id,
            'badge_id' => 101,
        ]);
    }
}

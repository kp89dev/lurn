<?php
namespace Tests\Unit\Listerners\Account\Normal;

use App\Events\User\UserMerged;
use App\Listeners\Account\Normal\AdjustUserBadges;
use App\Models\Badge;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdjustUserBadgesTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function handle_successfully_moves_the_merged_user_badges()
    {
        /** @var User $user */
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);

        $badges = factory(Badge::class, 2)->create();

        $userToMerge = factory(User::class)->create([
            'email'      => 'an_user@import.com',
        ]);

        foreach ($badges as $badge) {
            $user->badges()->attach($badge->id);
        }
        
        $event    = new UserMerged($user, $userToMerge);
        $listener = new AdjustUserBadges();
        $listener->handle($event);

        foreach ($badges as $badge) {
            $this->assertDatabaseHas('user_badges', [
                'user_id'  => $user->id,
                'badge_id' => $badge->id,
            ]);
        }
    }
}

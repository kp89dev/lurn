<?php
namespace Tests\Unit\Listerners\Account\Normal;

use App\Models\LessonSubscriptions;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Events\User\UserMerged;
use App\Models\User;
use App\Listeners\Account\Normal\AdjustLessonSubscription;

class AdjustLessonSubscriptionTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function handle_successfully_moves_lesson_subscriptions()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);

        //generate 1 users to merge
        $userToMerge = factory(User::class)->create([
            'email'      => 'imported@import.com'
        ]);

        //generate 2 users tests
        $lessonSubscriptions = factory(LessonSubscriptions::class, 2)->create([
            'user_id'    => $userToMerge->id,
            'from_table' => 'inbox'
        ]);
        $lessonSubscriptions->merge(factory(LessonSubscriptions::class, 2)->create([
            'user_id'    => $userToMerge->id,
            'from_table' => 'other'
        ]));

        //generate
        $nonMergebleLS = factory(LessonSubscriptions::class)->create([
            'user_id'    => $userToMerge->user_id + 1,
            'from_table' => 'inbox'
        ]);

        $event    = new UserMerged($user, $userToMerge);
        $listener = new AdjustLessonSubscription();
        $listener->handle($event);

        foreach ($lessonSubscriptions as $ls) {
            $this->assertDatabaseHas('lesson_subscriptions', [
                'user_id'   => $user->id,
                'id'        => $ls->id
            ]);
        }

        $this->assertDatabaseHas('lesson_subscriptions', [
            'user_id'   => $userToMerge->user_id + 1,
            'id'        => $nonMergebleLS->id
        ]);

        self::assertEquals(4, $user->lessonSubscriptions()->count());
    }
}

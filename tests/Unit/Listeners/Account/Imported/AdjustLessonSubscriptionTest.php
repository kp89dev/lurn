<?php
namespace Tests\Unit\Listerners\Account\Imported;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Listeners\Account\Imported\AdjustLessonSubscription;
use App\Events\User\ImportedUserMerged;
use App\Models\LessonSubscriptions;
use App\Models\ImportedUser;
use App\Models\User;

class AdjustLessonSubscriptionTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function handle_successfully_moves_imported_lesson_subscriptions()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);

        //generate 1 users to merge
        $userToMerge = factory(ImportedUser::class)->create([
            'email'      => 'imported@import.com',
            'connection' => 'inbox'
        ]);

        //generate 2 users tests
        $lessonSubscription = factory(LessonSubscriptions::class, 2)->create([
            'user_id' => $userToMerge->user_id,
            'from_table' => 'inbox'
        ]);

        //generate
        $nonMergebleLS = factory(LessonSubscriptions::class)->create([
            'user_id'    => $userToMerge->user_id + 1,
            'from_table' => 'inbox'
        ]);

        $event    = new ImportedUserMerged($user, $userToMerge);
        $listener = new AdjustLessonSubscription();
        $listener->handle($event);

        foreach ($lessonSubscription as $ls) {
            $this->assertDatabaseHas('lesson_subscriptions', [
                'user_id'   => $user->id,
                'id'        => $ls->id
            ]);
        }

        $this->assertDatabaseHas('lesson_subscriptions', [
            'user_id'   => $userToMerge->user_id + 1,
            'id'        => $nonMergebleLS->id
        ]);

        self::assertEquals(2, $user->lessonSubscriptions()->count());
    }
}

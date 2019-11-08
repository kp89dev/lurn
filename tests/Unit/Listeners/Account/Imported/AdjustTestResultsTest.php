<?php
namespace Tests\Unit\Listerners\Account\Imported;

use App\Events\User\ImportedUserMerged;
use App\Models\ImportedUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\TestResult;
use App\Models\User;
use App\Listeners\Account\Imported\AdjustTestUsers;

class AdjustTestResultsTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function handle_successfully_moves_the_imported_user_test_results()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);

        //generate 1 users to merge
        $userToMerge = factory(ImportedUser::class)->create([
            'email'      => 'imported@import.com',
            'connection' => 'inbox'
        ]);

        //generate 2 users tests
        $testResults = factory(TestResult::class, 2)->create([
            'user_id' => $userToMerge->user_id,
            'from_table' => 'inbox'
        ]);

        //generate
        $nonMergebleTR = factory(TestResult::class)->create([
            'user_id'    => $userToMerge->user_id + 1,
            'from_table' => 'inbox'
        ]);

        $event    = new ImportedUserMerged($user, $userToMerge);
        $listener = new AdjustTestUsers();
        $listener->handle($event);

        foreach ($testResults as $tr) {
            $this->assertDatabaseHas('test_users', [
                'user_id'   => $user->id,
                'test_id'   => $tr->test_id,
                'id'        => $tr->id
            ]);
        }

        $this->assertDatabaseHas('test_users', [
            'user_id'   => $userToMerge->user_id + 1,
            'test_id'   => $nonMergebleTR->test_id,
            'id'        => $nonMergebleTR->id
        ]);

        self::assertEquals(2, $user->testResults()->count());
    }
}

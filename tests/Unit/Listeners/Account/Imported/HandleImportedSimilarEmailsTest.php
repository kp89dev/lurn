<?php
namespace Tests\Unit\Listeners\Account\Imported;

use App\Events\User\ImportedUserLoggedIn;
use App\Events\User\ImportedUserMerged;
use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Listeners\Account\Imported\HandleImportedSimilarEmails;
use Illuminate\Support\Facades\Event;

class HandleImportedSimilarEmailsTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function handle_merges_similar_emails_accounts()
    {
        Event::fake();
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);
        $userToMerge = factory(ImportedUser::class)->create([
            'email'      => 'imported@import.com',
            'connection' => 'inbox'
        ]);
        $user->mergedImportedAccounts()->attach($userToMerge, ['from_table' => 'users_import_all']);

        $otherUsersToMerge = factory(ImportedUser::class, 2)->create([
            'email'      => 'imported@import.com',
            'connection' => 'inbox' . random_int(1, 5)
        ]);

        $event = new ImportedUserLoggedIn($userToMerge, $user);
        $listener = new HandleImportedSimilarEmails();
        $listener->handle($event);


        $this->assertDatabaseHas('user_merges', [
            'merged_user_id' => $userToMerge->an_id,
            'into_user_id'   => $user->id
        ]);



        foreach($otherUsersToMerge as $otherU) {
            $this->assertDatabaseHas('user_merges', [
                'merged_user_id' => $otherU->an_id,
                'into_user_id'   => $user->id
            ]);

            Event::assertDispatched(ImportedUserMerged::class, function ($e) use ($user, $otherU) {
                return $e->importedUser->user_id == $otherU->user_id &&
                $user->id == $e->user->id;
            });
        }
    }
}

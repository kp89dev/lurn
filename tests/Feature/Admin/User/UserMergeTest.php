<?php
namespace Feature\Admin\User;

use App\Events\User\ImportedUserMerged;
use App\Events\User\UserMerged;
use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class UserMergeTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function imported_user_to_merge_isnt_found()
    {
        $mainUser = factory(User::class)->create();
        $userToMerge = factory(ImportedUser::class)->make();

        $response = $this->post(route('users.merge', [
            'main_user'     => $mainUser->toArray(),
            'user_to_merge' => $userToMerge->toArray() + ['id' => 1000]
        ]));

        $response->assertStatus(412);
        self::assertTrue(Str::contains($response->getContent(), 'Failed!'));
    }

    /**
     * @test
     */
    public function imported_user_already_merged_in_the_same_user_isnt_allowed()
    {
        $mainUser = factory(User::class)->create();
        $userToMerge = factory(ImportedUser::class)->create();

        $mainUser->mergedImportedAccounts()
                 ->attach($userToMerge, ['from_table' => 'users_import_all']);

        $response = $this->post(route('users.merge', [
            'main_user'     => $mainUser->toArray(),
            'user_to_merge' => $userToMerge->toArray()
        ]));

        $response->assertStatus(412);
        self::assertTrue(Str::contains($response->getContent(), 'Failed!'));
    }

    /**
     * @test
     */
    public function imported_user_is_successfully_merged()
    {
        Event::fake();
        $mainUser = factory(User::class)->create();
        $userToMerge = factory(ImportedUser::class)->create();

        $response = $this->post(route('users.merge', [
            'main_user'     => $mainUser->toArray(),
            'user_to_merge' => $userToMerge->toArray()
        ]));

        $response->assertStatus(200);
        self::assertTrue(Str::contains($response->getContent(), 'User merged'));

        $this->assertDatabaseHas('user_merges', [
            'merged_user_id' => $userToMerge->an_id,
            'into_user_id'   => $mainUser->id,
            'from_table'     => 'users_import_all'
        ]);

        Event::assertDispatched(ImportedUserMerged::class, function ($e) use ($mainUser, $userToMerge) {
            return $e->user->id === $mainUser->id && $e->importedUser->id === $userToMerge->id;
        });
    }

    /**
     * @test
     */
    public function user_to_merge_isnt_found()
    {
        $mainUser = factory(User::class)->create();
        $userToMerge = factory(User::class)->make();

        $response = $this->post(route('users.merge', [
            'main_user'     => $mainUser->toArray(),
            'user_to_merge' => $userToMerge->toArray() + ['id' => 99]
        ]));

        $response->assertStatus(412);
        self::assertTrue(Str::contains($response->getContent(), 'Failed!'));
    }

    /**
     * @test
     */
    public function user_already_merged_in_the_same_user_isnt_allowed()
    {
        $mainUser = factory(User::class)->create();
        $userToMerge = factory(User::class)->create();

        $mainUser->mergedImportedAccounts()
            ->attach($userToMerge, ['from_table' => 'users']);

        $response = $this->post(route('users.merge', [
            'main_user'     => $mainUser->toArray(),
            'user_to_merge' => $userToMerge->toArray()
        ]));

        $response->assertStatus(412);
        self::assertTrue(Str::contains($response->getContent(), 'Failed!'));
    }

    /**
     * @test
     */
    public function user_is_successfully_merged()
    {
        Event::fake();
        $mainUser = factory(User::class)->create();
        $userToMerge = factory(User::class)->create();

        $response = $this->post(route('users.merge', [
            'main_user'     => $mainUser->toArray(),
            'user_to_merge' => $userToMerge->toArray()
        ]));

        $response->assertStatus(200);
        self::assertTrue(Str::contains($response->getContent(), 'User merged'));

        $this->assertDatabaseHas('user_merges', [
            'merged_user_id' => $userToMerge->id,
            'into_user_id'   => $mainUser->id,
            'from_table'     => 'users'
        ]);

        Event::assertDispatched(UserMerged::class, function ($e) use ($mainUser, $userToMerge) {
            return $e->user->id === $mainUser->id && $e->userMerged->id === $userToMerge->id;
        });
    }
}

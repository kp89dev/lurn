<?php
namespace Feature\Account;

use App\Events\User\ImportedUserMerged;
use App\Events\User\UserMerged;
use App\Models\AccountMergeToken;
use App\Models\ImportedUser;
use App\Models\User;
use App\Notifications\Account\AccountMergeConfirmation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AccountMergeTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function merge_page_is_available()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
                    ->get(route('account-merge.index'));

        $response->assertStatus(200)
                ->assertSee('check')
                ->assertSee($user->email);
    }
    
    /**
     * @test
     */
    public function search_returns_user_from_users_table()
    {
        $user = factory(User::class)->create();
        factory(ImportedUser::class, 5)->create();
        $aUser = factory(User::class, 5)->create();

        $response = $this->actingAs($user)
                         ->get(route('account-merge.search', ['email' => $aUser[0]->email]));

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => $aUser[0]->email]);
    }

    /**
     * @test
     */
    public function search_returns_user_from_imported_users_table()
    {
        $user = factory(User::class)->create();
        $aUser = factory(ImportedUser::class, 5)->create();

        $response = $this->actingAs($user)
                         ->get(route('account-merge.search', ['email' => $aUser[0]->email]));

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => $aUser[0]->email]);
    }

    /**
     * @test
     */
    public function search_returns_no_user_when_logged_in_user_is_search()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('account-merge.search', ['email' => $user->email]));

        $response->assertStatus(200)
            ->assertExactJson(['data' => []]);
    }

    /**
     * @test
     */
    public function search_returns_no_user_when_doesnt_exist()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('account-merge.search', ['email' => 'test@test.com']));

        $response->assertStatus(200)
            ->assertExactJson(['data' => []]);
    }

    /**
     * @test
     */
    public function initiateMerge_returns_error_when_user_is_no_found()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                ->post(route('account-merge.initiate', [
                    'email' => 'test@test.com'
                ]));

        $response->assertStatus(200)
                 ->assertSee('error')
                 ->assertJson(['data' => null]);
    }

    /**
     * @test
     */
    public function initiateMerge_successfully_creates_token()
    {
        Notification::fake();

        $user = factory(User::class)->create();
        $aUserToMerge = factory(ImportedUser::class)->create();

        $response = $this->actingAs($user)
            ->post(route('account-merge.initiate', [
                'email' => $aUserToMerge->email
            ]));

        $response->assertStatus(200)
                 ->assertSee('success');

        $this->assertDatabaseHas('account_merge_tokens', [
            'email_owner'    => $user->email,
            'email_to_merge' => $aUserToMerge->email
        ]);

        Notification::assertSentTo(
            $aUserToMerge,
            AccountMergeConfirmation::class,
            function ($notification, $channels) {
                return true;
            }
        );
    }

    /**
     * @test
     */
    public function proceedMerge_errors_when_token_is_not_found()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('account-merge.confirm', [
                'token' => 'asdasd123'
            ]));

        $response->assertStatus(302)
                ->assertSessionHas('errors');
    }

    /**
     * @test
     */
    public function proceedMerge_errors_when_token_expires()
    {
        $user = factory(User::class)->create();
        $aUserToMerge = factory(ImportedUser::class)->create();

        $tokenModel = new AccountMergeToken();
        $tokenModel->created_at = Carbon::now()->subSeconds(61);
        $token      = $tokenModel->getNewToken($user, $aUserToMerge);
        $tokenModel->save();

        $response = $this->actingAs($user)
            ->get(route('account-merge.confirm', [
                'token' => $token
            ]));

        $response->assertStatus(302)
            ->assertSessionHas('errors');
    }

    /**
     * @test
     */
    public function proceedMerge_successfully_merges_users()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $usersToMerge = factory(ImportedUser::class, 4)->create([
            'email' => 'imported@import.com',
            'connection' => 'some_' . Str::random(5)
        ]);
        $users = factory(User::class, 1)->create([
            'email' => 'imported@import.com'
        ]);

        $tokenModel = new AccountMergeToken();
        $token      = $tokenModel->getNewToken($user, $usersToMerge[0]);
        $tokenModel->save();

        $response = $this->actingAs($user)
            ->get(route('account-merge.confirm', [
                'token' => $token
            ]));


        $response->assertStatus(302)
            ->assertSessionMissing('errors')
            ->assertSessionHas(['message']);

        foreach ($usersToMerge as $uMerged) {
            $this->assertDatabaseHas('user_merges', [
                'merged_user_id' => $uMerged->an_id,
                'into_user_id'   => $user->id,
                'from_table'     => 'users_import_all'
            ]);

            Event::assertDispatched(ImportedUserMerged::class, function ($e) use ($user, $uMerged) {
                return $e->importedUser->an_id == $uMerged->an_id &&
                       $user->id == $e->user->id;
            });
        }

        foreach ($users as $aUser) {
            $this->assertDatabaseHas('user_merges', [
                'merged_user_id' => $aUser->id,
                'into_user_id'   => $user->id,
                'from_table'     => 'users'
            ]);

            Event::assertDispatched(UserMerged::class, function ($e) use ($user, $aUser) {
                return $e->userMerged->an_id == $aUser->an_id &&
                       $user->id == $e->user->id;
            });
        }
    }

    /**
     * @test
     */
    public function proceedMerge_successfully_merges_users_with_owner()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $usersToMerge = factory(ImportedUser::class, 4)->create([
            'email' => 'imported@import.com',
            'connection' => 'some_' . Str::random(5)
        ]);

        $owner = factory(ImportedUser::class)->create([
            'email' => $user->email,
            'connection' => 'some_' . Str::random(5)
        ]);
        $usersToMerge->push($owner);

        $users = factory(User::class, 1)->create([
            'email' => 'imported@import.com'
        ]);

        $tokenModel = new AccountMergeToken();
        $token      = $tokenModel->getNewToken($user, $owner);
        $tokenModel->save();

        $response = $this->actingAs($user)
            ->get(route('account-merge.confirm', [
                'token' => $token
            ]));


        $response->assertStatus(302)
            ->assertSessionMissing('errors')
            ->assertSessionHas(['message']);

        foreach ($usersToMerge as $uMerged) {
            if ($uMerged->id !== $owner->id) {
                $this->assertDatabaseHas('user_merges', [
                    'merged_user_id' => $uMerged->an_id,
                    'into_user_id'   => $user->id,
                    'from_table'     => 'users_import_all'
                ]);

                Event::assertDispatched(ImportedUserMerged::class, function ($e) use ($user, $uMerged) {
                    return $e->importedUser->an_id == $uMerged->an_id &&
                        $user->id == $e->user->id;
                });
            }
        }
    }
}

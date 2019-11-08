<?php
namespace Tests\Auth;

use App\Events\User\ImportedUserLoggedIn;
use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class ResetPasswordTest extends \TestCase
{
    use DatabaseTransactions;
    /**
     * @test
     */
    public function reset_password_page_is_available()
    {
        $response = $this->get(route('password.reset', ['token' => str_random()]));

        $response->assertSee('E-Mail Address')
            ->assertSee('Password')
            ->assertSee('Confirm Password')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function invalid_token_is_rejected()
    {
        $tokenRepo = $this->createTokenRepository();
        $tokenRepoImported = $this->createTokenRepository('password_resets_for_imports');

        $user = factory(User::class)->create([
            'email' => 'lurn@user.com'
        ]);
        $importedUser = factory(ImportedUser::class)->create([
            'email' => 'imported@lurn.com'
        ]);

        $tokenRepo->create($user);
        $tokenRepoImported->create($importedUser);

        $token = str_random();
        $referer = route('password.reset', ['token' => $token]);
        $response = $this->post(url('password/reset'), [
            'email'                 => 'lurn@user.com',
            'token'                 => $token,
            'password'              => 'asdasd123',
            'password_confirmation' => 'asdasd123'
            ],
            ['HTTP_REFERER' => $referer]
        );

        $response->assertRedirect($referer)
                 ->assertSessionHasErrors([
                    'email' => "This password reset token is invalid."
                 ]);

        $response = $this->post(url('password/reset'), [
            'email'                 => 'imported@lurn.com',
            'token'                 => $token,
            'password'              => 'asdasd123',
            'password_confirmation' => 'asdasd123'
        ],
            ['HTTP_REFERER' => $referer]
        );

        $response->assertRedirect($referer)
            ->assertSessionHasErrors([
                'email' => "This password reset token is invalid."
            ]);
    }

    /**
     * @test
     */
    public function valid_token_and_email_successfully_resets_password_users_table()
    {
        Event::fake();
        $tokenRepo = $this->createTokenRepository();
        $user = factory(User::class)->create([
            'email' => 'lurn@user.com'
        ]);

        $tokenUsersTable = $tokenRepo->create($user);

        $referer = route('password.reset', ['token' => $tokenUsersTable]);
        $response = $this->post(url('password/reset'), [
            'email'                 => 'lurn@user.com',
            'token'                 => $tokenUsersTable,
            'password'              => 'asdasd123',
            'password_confirmation' => 'asdasd123'
        ],
            ['HTTP_REFERER' => $referer]
        );

        $response->assertRedirect(url('/dashboard'))
                 ->assertSessionHas(['status' => "Your password has been reset!"])
                 ->assertSessionMissing('errors');


    }

    /**
     * @test
     */
    public function valid_token_and_email_successfully_resets_password_imported_user()
    {
        Event::fake();
        $tokenRepoImported = $this->createTokenRepository('password_resets_for_imports');
        $importedUser = factory(ImportedUser::class)->create([
            'email' => 'imported@lurn.com'
        ]);
        $tokenUsersImportedTable = $tokenRepoImported->create($importedUser);

        $referer = route('password.reset', ['token' => $tokenUsersImportedTable]);
        $response = $this->post(url('password/reset'), [
            'email'                 => 'imported@lurn.com',
            'token'                 => $tokenUsersImportedTable,
            'password'              => 'asdasd123',
            'password_confirmation' => 'asdasd123'
        ], ['HTTP_REFERER' => $referer]);

        $this->assertDatabaseHas('users', ['email' => 'imported@lurn.com']);
        $this->assertDatabaseHas('user_merges', [
            'merged_user_id' => $importedUser->an_id,
            'from_table'     => 'users_import_all'
        ]);
        Event::assertDispatched(ImportedUserLoggedIn::class, function ($e) use ($importedUser) {
            return $e->importedUser->id === $importedUser->id;
        });
        $response->assertRedirect(url('/dashboard'))
            ->assertSessionHas(['status' => "Your password has been reset!"])
            ->assertSessionMissing('errors');
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected function createTokenRepository($table = 'password_resets')
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return new DatabaseTokenRepository(
            $this->app['db']->connection(),
            $this->app['hash'],
            $table,
            $key,
            60
        );
    }
}

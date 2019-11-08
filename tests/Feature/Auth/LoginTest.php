<?php

namespace Tests\Auth;

use App\Events\User\ImportedUserLoggedIn;
use App\Events\User\ImportedUserMerged;
use App\Listeners\Auth\IncrementFailedLogins;
use App\Models\ImportedUser;
use App\Models\User;
use App\Services\Tracker\Contracts\LocationReader;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

class LoginTest extends \TestCase
{
    /**
     * @test
     */
    public function login_page_is_available()
    {
        $response = $this->get('/login')
            ->assertSee('E-Mail Address')
            ->assertSee('Password');
        //->assertSee('Remember Me');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function login_fails_with_wrong_credentials()
    {
        $this->expectException(ValidationException::class);

        $response = $this->post(route('login'), [
            'email'    => 'marius123456@gmail.com',
            'password' => 'secret',
        ], ['HTTP_REFERER' => route('login')]);

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function login_fails_when_using_wrong_password()
    {
        $this->expectException(ValidationException::class);

        $this->mockFailedLoginListener();

        factory(ImportedUser::class)->create([
            'email'       => 'auser@user.com',
            'password'    => bcrypt('secret'),
            'md5password' => md5('secret' . 'salt'),
            'salt'        => 'salt',
        ]);

        factory(User::class)->create([
            'email' => 'imported@user.com',
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'auser@user.com',
            'password' => 'secret2',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $response->assertRedirect(url('/login'));

        $response = $this->post(route('login'), [
            'email'    => 'imported@user.com',
            'password' => 'secret2',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $response->assertRedirect(url('/login'));
    }

    /**
     * @test
     */
    public function login_fails_when_user_is_soft_deleted()
    {
        $this->expectException(ValidationException::class);

        $user = factory(User::class)->create([
            'email'      => 'deleted@user.com',
            'deleted_at' => Carbon::now()->subMinutes(1),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'deleted@user.com',
            'password' => 'secret',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function login_fails_when_user_was_imported_already()
    {
        $this->expectException(ValidationException::class);

        $this->mockFailedLoginListener();
        factory(ImportedUser::class)->create([
            'email'       => 'imported@user.com',
            'password'    => bcrypt('secret'),
            'md5password' => md5('secret' . 'salt'),
            'salt'        => 'salt',
        ]);

        factory(User::class)->create([
            'email' => 'imported@user.com',
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'imported@user.com',
            'password' => 'secret2',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $response->assertRedirect(url('/login'));
    }

    /**
     * @test
     */
    public function login_is_throttled_after_5_attempts()
    {
        $this->expectException(ValidationException::class);

        for ($i = 0; $i <= 2; $i++) {
            $response = $this->post(route('login'), [
                'email'    => 'user@user.com',
                'password' => str_random(5),
            ],
                ['HTTP_REFERER' => route('login')]
            );

            $response->assertRedirect(route('login'));
        }

        $response = $this->post(route('login'), [
            'email'    => 'user@user.com',
            'password' => str_random(5),
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('errors');
    }


    /**
     * @test
     */
    public function successful_login_with_user_in_users_table()
    {
        Event::fake();
        $user = factory(User::class)->create(['email' => 'valid@user.com', 'status' => 1]);

        $response = $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'secret',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * @test
     */
    public function successful_login_with_user_from_users_import_table_using_bcryp_password()
    {
        Event::fake();
        $user = factory(ImportedUser::class)->create([
            'email'       => 'imported@user.com',
            'password'    => bcrypt($password = 'secret'),
            'md5password' => md5($password . 'salt'),
            'salt'        => 'salt',
        ]);

        $response = $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'secret',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('user_merges', [
            'merged_user_id' => $user->an_id,
            'from_table'     => 'users_import_all',
        ]);

        Event::assertDispatched(ImportedUserLoggedIn::class, function ($e) use ($user) {
            return $e->importedUser->an_id === $user->an_id;
        });

        Event::assertDispatched(ImportedUserMerged::class, function ($e) use ($user) {
            return $e->importedUser->user_id === $user->user_id
                && $e->user->email == $user->email;
        });

        $response->assertRedirect(url('/dashboard'));
    }

    /**
     * @test
     */
    public function successful_login_with_user_from_users_import_table_using_md5_password()
    {
        Event::fake();
        $salt = str_random(5);
        $user = factory(ImportedUser::class)->create([
            'email'       => 'importedmd5@user.com',
            'password'    => bcrypt('test'),
            'md5password' => md5('secret' . $salt),
            'salt'        => $salt,
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'importedmd5@user.com',
            'password' => 'secret',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $this->assertDatabaseHas('users', [
            'email' => 'importedmd5@user.com',
        ]);
        $this->assertDatabaseHas('user_merges', [
            'merged_user_id' => $user->an_id,
            'from_table'     => 'users_import_all',
        ]);

        Event::assertDispatched(ImportedUserLoggedIn::class, function ($e) use ($user) {
            return $e->importedUser->user_id === $user->user_id;
        });

        Event::assertDispatched(ImportedUserMerged::class, function ($e) use ($user) {
            return $e->importedUser->user_id === $user->user_id &&
                $e->user->email == $user->email;
        });

        $response->assertRedirect(url('/dashboard'));
    }

    /**
     * @test
     */
    public function login_denied_for_imported_already_merged_user()
    {
        $this->expectException(ValidationException::class);

        Event::fake();
        $mainUser = factory(User::class)->create(['email' => 'valid@user.com']);
        $userToMerge = factory(ImportedUser::class)->create([
            'email'    => 'imported@user.com',
            'password' => bcrypt('secret'),
        ]);

        $mainUser->mergedImportedAccounts()
            ->attach($userToMerge, ['from_table' => 'users_import_all']);

        $response = $this->post(route('login'), [
            'email'    => 'imported@user.com',
            'password' => 'secret',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $response->assertRedirect(route('login'))
            ->assertSessionHas('errors');

        $errorMessage = app('session.store')->get('errors')->default->get('email')[0];
        $this->assertContains('valid@user.com', $errorMessage);
        $this->assertContains('imported@user.com', $errorMessage);
    }

    /**
     * @test
     */
    public function login_denied_for_main_user_when_is_merged()
    {
        $this->expectException(ValidationException::class);

        Event::fake();
        $mainUser = factory(User::class)->create(['email' => 'valid@user.com']);
        $userToMerge = factory(User::class)->create([
            'email'    => 'merged@user.com',
            'password' => bcrypt('secret'),
        ]);

        $mainUser->mergedAccounts()
            ->attach($userToMerge, ['from_table' => 'users']);

        $response = $this->post(route('login'), [
            'email'    => 'merged@user.com',
            'password' => 'secret',
        ],
            ['HTTP_REFERER' => route('login')]
        );

        $response->assertRedirect(route('login'))
            ->assertSessionHas('errors');

        $errorMessage = app('session.store')->get('errors')->default->get('email')[0];
        $this->assertContains('valid@user.com', $errorMessage);
        $this->assertContains('merged@user.com', $errorMessage);
    }

    /**
     * @test
     */
    public function increments_failed_logins()
    {
        $this->expectException(ValidationException::class);

        $this->mockLocationReader();

        $user = factory(User::class)->create();
        $this->post(route('login'), ['email' => $user->email, 'password' => uniqid()]);

        $this->assertDatabaseHas('user_logins', ['user_id' => $user->id, 'successful' => 0]);
    }

    /**
     * @test
     */
    public function increments_successful_logins()
    {
        $this->withExceptionHandling();

        $this->mockLocationReader();
        $user = factory(User::class)->create(['password' => bcrypt('test')]);
        $this->post(route('login'), ['email' => $user->email, 'password' => 'test']);

        $this->assertDatabaseHas('user_logins', ['user_id' => $user->id, 'successful' => 1]);
    }

    private function mockFailedLoginListener()
    {
        $failedLoginListener = $this->createMock(IncrementFailedLogins::class);
        $failedLoginListener->expects(self::any())
            ->method('handle')
            ->willReturn(true);
        $this->app->bind(IncrementFailedLogins::class, function ($app) use ($failedLoginListener) {
            return $failedLoginListener;
        });
    }

    private function mockLocationReader()
    {
        $location = $this->createMock(LocationReader::class);

        $this->app->bind(LocationReader::class, function ($app) use ($location) {
            return new class()
            {
                public function __get($param)
                {
                    return $param == 'subdivisions' ? [0 => uniqid()] : new class()
                    {
                        public function __get($param)
                        {
                            return uniqid();
                        }
                    };
                }
            };
        });
    }

    /**
     * @test

    public function token_mismatch_returns_with_error()
     * {
     * $user = factory(User::class)->create(['password' => bcrypt('test')]);
     *
     * $response = $this->session(['_token', 'DONKEY'])
     * ->post(route('login'), [
     * 'email'     => $user->email,
     * 'password'  => 'test',
     * '_token'    => 'evenworsetoken'
     * ]);
     *
     * $response->assertRedirect('/')
     * ->assertSessionHas('errors');
     * }*/
}

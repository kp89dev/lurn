<?php
namespace Tests\Featire\Auth;

use App\Models\ImportedUser;
use App\Models\User;
//use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\Account\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;

class ForgotPasswordTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function forgot_password_page_is_available()
    {
        $response = $this->get(route('password.request'));

        $response->assertSee('E-Mail Address')
                 ->assertSee('Recover Your Account')
                 ->assertStatus(200);
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function invalid_email_is_rejected()
    {
        $response = $this->post(
                    url('password/email'), [
                        'email' => 'invalid_email',
                    ],
                    ['HTTP_REFERER' => route('password.request')]
                );

        $response->assertRedirect(route('password.request'))
            ->assertSessionHasErrors([
                'email' => 'The email must be a valid email address.'
            ]);
    }

    /**
     * @test
     */
    public function non_existing_user_is_rejected()
    {
        $response = $this->post(
                url('password/email'), [
                'email' => 'invalid@email.com',
            ],
            ['HTTP_REFERER' => route('password.request')]
        );
        
        $response->assertRedirect(route('password.request'))
                ->assertSessionHasErrors([
                    'email' => "We can't find a user with that e-mail address."
                ]);
    }

    /**
     * @test
     */
    public function reset_link_is_sent_succesfully_for_user_in_users_table()
    {
        $user = factory(User::class)->create([
            'email' => 'user@lurn.com'
        ]);
        Notification::fake();

        $response = $this->post(
            url('password/email'), [
                'email' => 'user@lurn.com',
            ],
            ['HTTP_REFERER' => route('password.request')]
        );

        $this->assertDatabaseHas('password_resets', ['email' => 'user@lurn.com']);
        Notification::assertSentTo($user, ResetPassword::class);

        $response->assertRedirect(route('password.request'))
            ->assertSessionMissing('errors');
        
    }

    /**
     * @test
     */
    public function reset_link_is_sent_succesfully_for_user_in_imported_all_users_table()
    {
        $user = factory(ImportedUser::class)->create([
            'email' => 'user_imported@lurn.com'
        ]);
        Notification::fake();

        $response = $this->post(
            url('password/email'), [
                'email' => 'user_imported@lurn.com',
            ],
            ['HTTP_REFERER' => route('password.request')]
        );

        $this->assertDatabaseHas('password_resets_for_imports', ['email' => 'user_imported@lurn.com']);
        Notification::assertSentTo($user, ResetPassword::class);


        $response->assertRedirect(route('password.request'))
                 ->assertSessionMissing('errors');
    }
}

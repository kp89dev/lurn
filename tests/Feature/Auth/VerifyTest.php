<?php

namespace Tests\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;

class VerifyTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @expectedException  \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function invalid_verification_code_returns_404()
    {
        $this->get(route('verify', 'invalid_encrypted_id'))
            ->assertStatus(404);
    }

    /**
     * @test
     *
     * @expectedException  \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function invalid_encrypted_user_id_returns_404()
    {
        $user = factory(User::class)->make(['id' => -1]);

        $this->get(route('verify', Crypt::encrypt($user->id)))
            ->assertStatus(404);
    }

    /**
     * @test
     */
    public function account_gets_verified()
    {
        Event::fake();
        $user = factory(User::class)->create(['status' => 0]);

        $this->get(route('verify', Crypt::encrypt($user->id)))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Your account has been successfully verified!');
    }

    /**
     * @test
     */
    public function resend_verification_redirects_when_loggedin()
    {
        $user = factory(User::class)->create(['status' => 1]);

        $this->actingAs($user)
            ->get(route('resend-verification'))
            ->assertRedirect(route('dashboard'));
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function resend_verification_is_validated()
    {
        $this->post(route('resend-verification'), [])
            ->assertSessionHasErrors('email');

        $this->post(route('resend-verification'), ['email' => 'invalid'])
            ->assertSessionHasErrors('email');

        $this->post(route('resend-verification'), ['email' => 'inexistent@valid.ema.il'])
            ->assertSessionHasErrors('email');

        $user = factory(User::class)->create(['status' => 0]);

        $this->post(route('resend-verification'), ['email' => $user->email])
            ->assertSessionHas('success');
    }
}

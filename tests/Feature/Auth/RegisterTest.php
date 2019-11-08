<?php
namespace Tests\Feature\Auth;

use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;

class RegisterTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function registration_page_is_available()
    {
        $response = $this->get(route('register'))
            ->assertSee('Register')
            ->assertSee('Email')
            ->assertSee('Password');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function register_fails_when_email_already_exists_in_users_table()
    {
        factory(User::class)->create([
            'email' => 'test@user.com'
        ]);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@test.com'
        ]);

        $response->assertStatus(302)
                 ->assertSessionHas('errors');
    }

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function register_fails_when_email_already_exists_in_imported_users_table()
    {
        factory(ImportedUser::class)->create([
            'email' => 'test@user.com'
        ]);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@test.com'
        ]);

        $response->assertStatus(302)
            ->assertSessionHas('errors');
    }

    /**
     * @test
     */
    public function register_creates_user_successsfully()
    {
        Event::fake();

        $response = $this->post('/register', [
            'name'                  => 'test',
            'email'                 => 'test@test.com',
            'password'              => 'test123',
            'password_confirmation' => 'test123'
        ]);

        Event::assertDispatched(Registered::class, function ($e) {
            return $e->user->email === 'test@test.com';
        });

        $response->assertStatus(302)
                 ->assertSessionMissing('errors');

        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
            'name'  => 'test'
        ]);
    }

}

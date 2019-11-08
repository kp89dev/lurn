<?php

namespace Tests\Feature\Auth;

use App\Listeners\Auth\IncrementSuccessfulLogins;
use App\Models\User;

class AdminImpersonationTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function admin_can_login_as_user()
    {
        $successfulLoginMock = $this->createMock(IncrementSuccessfulLogins::class);
        $successfulLoginMock->expects(self::once())
                ->method('handle')
                ->willReturn(true);
        $this->app->bind(IncrementSuccessfulLogins::class, function($app) use ($successfulLoginMock) {
            return $successfulLoginMock;
        });

        $user = factory(User::class)->create();


        $response = $this->post(route('users.impersonate'), [
            'user' => $user->id,
        ]);
        
        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('admin_impersonator');
    }

}

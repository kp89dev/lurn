<?php
namespace Tests\Admin\User;

use App\Models\ImportedUser;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;

class LoginsTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function logs_are_listed()
    {
        Event::fake();
        $user = factory(User::class)->create();
        $logs = factory(UserLogin::class, 5)->create(['user_id' => $user->id]);
        $response = $this->get(route('user-logins.index'));


        foreach ($logs as $log) {
            $response
                ->assertSeeText(htmlspecialchars($user->name, ENT_QUOTES))
                ->assertSeeText(htmlspecialchars($log->city, ENT_QUOTES))
                ->assertSeeText(htmlspecialchars($log->regionName, ENT_QUOTES))
                ->assertSeeText($log->timezone);
        }
    }
}

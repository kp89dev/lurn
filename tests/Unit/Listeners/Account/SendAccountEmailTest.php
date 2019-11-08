<?php
namespace Tests\Unit\Listeners\Account;

use App\Events\User\UserCreatedThroughInfusionsoft;
use App\Events\User\UserCreatedThroughAdmin;
use App\Listeners\Account\SendAccountEmail;
use App\Mail\UserRegisteredPasswordReset;
use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Mail;

class SendAccountEmailTest extends \TestCase
{
    /**
     * @test
     */
    public function handle_sends_successfully_the_email_infusionsoft()
    {
        Mail::fake();
        $passwordBroker = $this->getMockBuilder(PasswordBroker::class)
                                ->disableOriginalConstructor()
                               ->getMock();
        $passwordBroker->expects($this->once())
                ->method('createToken')
                ->willReturn('asdasd123');

        $user = factory(User::class)->make();
        $event = new UserCreatedThroughInfusionsoft($user);

        $handler = new SendAccountEmail($passwordBroker);
        $handler->handle($event);

        Mail::assertSent(UserRegisteredPasswordReset::class, function($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
    
    /**
     * @test
     */
    public function handle_sends_successfully_the_email_admin()
    {
        Mail::fake();
        $passwordBroker = $this->getMockBuilder(PasswordBroker::class)
                                ->disableOriginalConstructor()
                               ->getMock();
        $passwordBroker->expects($this->once())
                ->method('createToken')
                ->willReturn('asdasd123');

        $user = factory(User::class)->make();
        $event = new UserCreatedThroughAdmin($user);

        $handler = new SendAccountEmail($passwordBroker);
        $handler->handle($event);

        Mail::assertSent(UserRegisteredPasswordReset::class, function($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}

<?php
namespace App\Listeners\Account;

use App\Events\User\UserCreatedThroughInfusionsoft;
use App\Events\User\UserCreatedThroughAdmin;
use App\Mail\UserRegisteredPasswordReset;
use App\Models\EmailStatus;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Mail;

class SendAccountEmail
{
    /**
     * @type TokenRepositoryInterface
     */
    private $tokenRepo;

    public function __construct(PasswordBroker $passwordBroker)
    {
        $this->tokenRepo = $passwordBroker;
    }

    public function handle($event)
    {
        $token = $this->tokenRepo->createToken($event->user);

        $response = Mail::to($event->user)
            ->send(new UserRegisteredPasswordReset($token, $event->user->name));

        if($response) {
            $r = $response->getBody()->getContents();
            EmailStatus::create([
                'aws_id' => $r['_id'],
                'user_id' => $event->user->id,
                'status' => 0,
                'last_timestamp' => Carbon::now(),
                'subject' => "Password Reset Link",
            ]);
        }
    }

}

<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @type string
     */
    private $token;
    private $name;

    public function __construct(string $token, $name)
    {
        $this->token = $token;
        $this->name = $name;
    }

    /**
     * @codeCoverageIgnore
     * @return $this
     */
    public function build()
    {
        return $this->from(['email' => config('support.email'), 'name' => config('support.name')])
                    ->subject('Lurn Nation Account Created')
                    ->markdown('emails.register')->with([
                        'intro' => 'Welcome to Lurn Nation!',
                        'greeting' => sprintf('Hello, %s!', $this->name),
                        'beforeCTA' => 'You\'ve been registerd into #LurnNation<br/>You\'ll need to set your account password',
                        'action' => [
                            'url' => route('password.reset', ['token' => $this->token]),
                            'text' => 'Click here to set your account password'
                            ],
                        'subcopy' => 'Please don\'t respond to this email, and create instead a new email to the above email address if you wish to respond.'
                    ]);
    }
}

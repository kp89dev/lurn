<?php

namespace App\Console\Commands\Tasks;

use App\Mail\SendAbandonedCartReminder;
use App\Models\CartReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:cart-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches for users with abandoned carts and sends a reminder email.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reminders = CartReminder::whereAbandoned();

        if ($reminders->count()) {
            $reminders->each(function ($reminder) {
                Mail::to($reminder->user)->send(new SendAbandonedCartReminder($reminder));
            });
        }

        CartReminder::whereAbandoned()->delete();
    }
}

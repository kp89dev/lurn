<?php

namespace App\Console\Commands\Tasks;

use App\Mail\SendPaymentReminder;
use App\Models\CourseSubscriptions;
use App\Services\Logger\Cloudwatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionPaymentsReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:payment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches for payments that are about to happen in the next 2 days and sends a reminder email.';

    protected $log;

    public function __construct()
    {
        parent::__construct();

        $this->log = app(Cloudwatch::class, ['streamName' => 'subscription-payment-reminders']);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $subs = CourseSubscriptions::whereSubscriptionPayment(1)
            ->where('paid_at', '<=', now()->subDays(28)) // Last payment 28 days ago or more.
            ->where('notifications_sent', '<', raw('payments_made')) // Where we haven't notified yet.
            ->where('payments_made', '<', raw('payments_required')) // Subscriptions that haven't already been fully paid.
            ->whereIn('payments_required', [3, 4]) // Subscription payments that end soon.
            ->whereNull('cancelled_at'); // Enabled subscriptions.

        if ($noOfSubs = $subs->count()) {
            $this->log->info("Found $noOfSubs to update.");

            $subs->each(function ($sub) {
                $this->log->info(sprintf('Reminding %s of the next payment...', $sub->user->email));
            	Mail::to($sub->user)->send(new SendPaymentReminder($sub));
                $this->log->info('Done.');
            });
        }
    }
}

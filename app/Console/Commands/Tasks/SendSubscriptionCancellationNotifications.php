<?php

namespace App\Console\Commands\Tasks;

use App\Mail\SendSubscriptionCancellationNotice;
use App\Models\CourseSubscriptions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionCancellationNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:subscription-cancellations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Looks for users that haven't paid their subscriptions on time and sends cancellation reminders.";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sentTo = collect([]);
        $subs = CourseSubscriptions::whereSubscriptionPayment(1)
            ->where('payments_made', '<', raw('payments_required'))
            ->whereNull('cancelled_at');

        foreach ([8, 22, 29] as $dayDiff) {
            (clone $subs)
                ->where('paid_at', '<=', now()->subDays(30 + $dayDiff))
                ->each(function ($sub) use ($dayDiff, $sentTo) {
                    if (! $sentTo->contains($sub->user->id)) {
                        Mail::to($sub->user)->send(new SendSubscriptionCancellationNotice($sub, [
                            'remainingDays' => 30 - $dayDiff,
                            'paymentUrl'    => $sub->course->infusionsoft->subscription_payment_url,
                        ]));

                        $sentTo->push($sub->user->id);
                    }
                });
        }
    }
}

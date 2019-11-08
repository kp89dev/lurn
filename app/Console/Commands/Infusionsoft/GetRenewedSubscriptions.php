<?php

namespace App\Console\Commands\Infusionsoft;

use App\Jobs\Infusionsoft\ResponseHandlerTrait;
use App\Models\CourseSubscriptions;
use App\Services\Contracts\TrackerInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Infusionsoft\Infusionsoft;

class GetRenewedSubscriptions extends Command
{
    use ResponseHandlerTrait;

    protected $signature = 'subscriptions:renew';
    protected $description = 'Tries to update users subscriptions if rebill was successful in infusionsoft';

    public function handle()
    {
        $subscriptions = CourseSubscriptions
            ::join('course_infusionsoft', 'course_infusionsoft.course_id', '=', 'user_courses.course_id')
            ->where('user_courses.subscription_payment', 1)
            ->where('paid_at', '<', now()->subDays(29))
            ->where(function ($query) {
            	$query->whereNull('user_courses.payments_required')
                    ->orWhere('user_courses.payments_made', '<', raw('user_courses.payments_required'));
            })
            ->get();

        if (! $subscriptions->count()) {
            return Log::info('Renewing subscription finished. Nothing to renew.');
        }

        $successfulRenews = 0;
        $failedRenews = 0;

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            $courseInfusionsoft = $subscription->course->infusionsoft;
            $contact = $user->contactIdFor($courseInfusionsoft->is_account)->first();

            /** @var $is \Infusionsoft\Infusionsoft */
            $is = app(Infusionsoft::class, ['account' => $contact->is_account]);

            try {
                $this->response = $is->data()->query(
                    'RecurringOrder', // Table
                    10, // Results per page
                    0, // Page
                    [
                        'ProductId' => $subscription->is_product_id,
                        'ContactId' => $contact->is_contact_id,
                        'Status'    => 'Active',
                    ], // Filters
                    ['NextBillDate', 'LastBillDate', 'ContactId'], // Returned data
                    'Id', // Order column
                    false // Ascending
                );

                if (! count($this->response)) {

                    if ($this->checkIfUserPayedDiscountedProduct($subscription, $is)) {

                        $this->track('Subscription cancelled: Payed in full by discount url', [
                            'email' => $subscription->user->email,
                        ]);

                        continue;
                    }

                    $subscription->cancelled_at = now();
                    $subscription->cancelled_by = 0;
                    $subscription->cancelled_reaason = 'System: Subscription not found';

                    $subscription->save();

                    $this->track('Subscription cancelled: Not Found when renewing', [
                        'email' => $subscription->user->email,
                    ]);

                    continue;
                }

                $lastBillDate = Carbon::instance($this->response[0]['LastBillDate']);

                if ($lastBillDate->lessThan(Carbon::parse($subscription->paid_at))) {
                    $subscription->cancelled_at = now();
                    $subscription->cancelled_by = 0;
                    $subscription->cancelled_reaason = 'System: Unable to find valid re-bill';
                    $subscription->save();

                    $this->track('Subscription cancelled: Unable to find valid re-bill', [
                        'email' => $subscription->user->email,
                    ]);

                    continue;
                }

                $subscription->paid_at = $lastBillDate->toDateTimeString();
                $subscription->payments_made += 1;
                $subscription->save();

                $this->track('Subscription renewed', ['email' => $subscription->user->email]);
                $this->track('Payment', [
                    'amount'  => $subscription->course->infusionsoft->price,
                    'product' => $subscription->course->infusionsoft->is_product_id,
                    'upsell'  => $subscription->course->infusionsoft->upsell,
                ]);

                $successfulRenews += 1;
            } catch (\Exception $e) {
                $failedRenews += 1;
                $this->handleException($e);
            }
        }

        Log::info("Renewing subscription finished. Successfull $successfulRenews, failed $failedRenews");
    }

    protected function track($event, $extra)
    {
        static $woopra;

        $woopra || $woopra = app()->make(TrackerInterface::class)->make();

        $woopra->track($event, $extra);
    }

    private function checkIfUserPayedDiscountedProduct($subscription, $is)
    {
        if (! $subscription->is_subscription_discount_product_id) {
            return false;
        }

        $result = $is->order()
            ->where('contact_id', $subscription->user->infusionsoft->is_contact_id)
            ->where('product_id', $subscription->is_subscription_discount_product_id)
            ->where('paid', true)
            ->get('/orders');

        if ($result['count'] == 0) {
            return false;
        }

        $subscription->paid_at = Carbon::instance($result['order'][0]['creation_date']);
        $subscription->save();
    }
}


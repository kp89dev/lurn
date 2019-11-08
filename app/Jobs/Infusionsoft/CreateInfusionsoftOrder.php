<?php

namespace App\Jobs\Infusionsoft;

use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Infusionsoft\Infusionsoft;

class CreateInfusionsoftOrder implements InfusionsoftJobInterface
{
    use ResponseHandlerTrait;

    /**
     * @type Course
     */
    private $course;

    /**
     * @type \stdClass
     */
    private $card;

    /**
     * CreateInfusionsoftOrder constructor.
     *
     * @param Course    $course
     * @param \stdClass $card
     */
    public function __construct(Course $course, \stdClass $card)
    {
        $this->course = $course;
        $this->card = $card;
    }

    public function handle()
    {
        /** @type Infusionsoft $is */
        $is = app(Infusionsoft::class, ['account' => $this->course->infusionsoft->is_account]);
        list($products, $subscriptions) = $this->getProductAndSubscriptionPlan();

        try {
            $this->response = $is->orders('xml')->placeOrder(
                $this->card->ContactId,
                0,
                0, // Plan ID
                $products,
                $subscriptions,
                false,
                [],
                0,
                0
            );

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @return array
     */
    private function getProductAndSubscriptionPlan()
    {
        $paymentType = request('payment_type', 'full');

        if ($this->course->infusionsoft->subscription && $paymentType == 'subscription') {
            $products = [];
            $subscriptions = [$this->course->infusionsoft->is_subscription_product_id];
        } else {
            $products = [$this->course->infusionsoft->is_product_id];
            $subscriptions = [];
        }

        return [$products, $subscriptions];
    }
}

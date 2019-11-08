<?php

namespace App\Http\Controllers\Remote;

use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends AbstractRemoteController
{
    protected $expectedFields = [
        'Id',
        'Email',
        'LastName',
        'FirstName',
    ];

    public function extend($productId, Request $request)
    {
        $user = User::whereEmail($request->Email)->first();
        $subscription = $this->getSubscription();

        if ($user && $subscription) {
            // When paying the remaining payments at once.
            if (request('paid_in_full')) {
                $subscription->payments_made = $subscription->payments_required;

                $this->logger->info("$user->email paid the remaining payments all at once.");
            }

            // When making a normal payment.
            elseif ($subscription->payments_made < $subscription->payments_required) {
                $subscription->payments_made += 1;

                $this->logger->info("$user->email made a payment and extended their expiration date.");
            }

            $user->save();
        } else {
            // If the user is undefined, it usually means that the billing automation trigger got here first.
            // We're going to pass the necessary info to the remote register controller to create the new user.
            $request->merge([
                'contact_id'   => $request->Id,
                'product_id'   => $productId,
                'invoice_id'   => 0,
                'name'         => sprintf('%s %s', $request->FirstName, $request->LastName),
                'email'        => $request->Email,
                'subscription' => 1,
            ]);

            app(RegisterController::class)->index($request);

            $this->logger->info("Sent $request->Email to be registered because an account wasn't found for that email.");
        }
    }

    private function getSubscription(User $user, $courseId)
    {
        return $user->courses()->whereCourseId($courseId)->first() or abort(204, '');
    }
}

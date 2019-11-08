<?php
namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Jobs\Infusionsoft\CancelSubscription;
use App\Jobs\Infusionsoft\GetContactSubscriptionIdOnProduct;
use App\Models\CourseSubscriptions;
use Illuminate\Http\Request;

class CancelSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $courseSubscription = $user
            ->courseSubscriptions()
            ->where('course_id', $request->course_id)
            ->where('cancelled_at', null)
            ->first();

        if (! $courseSubscription instanceof CourseSubscriptions) {
            return redirect()->back()->withErrors(['Unable to cancel the subscription. Please contact the support']);
        }

        if ($courseSubscription->course->infusionsoft->subscription != 1) {
            return redirect()->back()->withErrors(['Unable to cancel the subscription. Invalid subscription']);
        }

        //get subscriptions of user
        $activeSubscriptions = $this->dispatchNow(
            new GetContactSubscriptionIdOnProduct(
                $user->contactIdFor($courseSubscription->course->infusionsoft->is_account)->first(),
                $courseSubscription->course->infusionsoft->is_product_id
            )
        );
        
        if ($activeSubscriptions->hasError() || !count($activeSubscriptions->getResponse())) {
            return redirect()->back()->withErrors(['Unable to cancel subscription. Please try again later']);
        }

        $cancelResponse = $this->dispatchNow(
            new CancelSubscription(
                $user->contactIdFor($courseSubscription->course->infusionsoft->is_account)->first(),
                $activeSubscriptions->getResponse()[0]['Id']
            )
        );

        if ($cancelResponse->hasError()) {
            return redirect()->back()->withErrors($cancelResponse->getError());
        }

        $courseSubscription->cancelled_at = now();
        $courseSubscription->cancelled_by = $user->id;
        $courseSubscription->cancelled_reason = 'Cancelled from account settings';
        
        $courseSubscription->save();

        return redirect()->back()->with(['success' => 'Subscription cancelled sucessfully']);
    }
}

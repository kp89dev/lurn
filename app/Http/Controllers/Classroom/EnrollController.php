<?php

namespace App\Http\Controllers\Classroom;

use App\Events\User\UserEnrolled;
use App\Http\Controllers\Controller;
use App\Jobs\Infusionsoft\AddContactCard;
use App\Jobs\Infusionsoft\CreateInfusionsoftOrder;
use App\Jobs\Infusionsoft\CreateUserContact;
use App\Jobs\Infusionsoft\GetUserCards;
use App\Jobs\Infusionsoft\GetUserContactId;
use App\Jobs\Infusionsoft\PayInfusionsoftOrder;
use App\Jobs\Infusionsoft\ValidateCard;
use App\Models\Course;
use App\Models\CourseUpsell;
use App\Models\CourseUpsellToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inacho\CreditCard;
use ReflectionMethod;
use Illuminate\Support\Facades\Session;

class EnrollController extends Controller
{

    /**
     * EnrollController constructor.
     */
    public function __construct()
    {
        $this->middleware('resolve.resources')->only('index', 'enroll');

        $this->middleware(function ($request, Closure $next) {
            if ($request->token) { //upsells
                $this->resolveInfusionsoftDetails($request);
            }

            view()->share([
                'course' => request('course'),
                'token'  => $request->token,
            ]);

            return $next($request);
        });

        $this->middleware(function ($request, Closure $next) {
            $this->seoSetup($request);

            return $next($request);
        });
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        if (! user()) {
            $redirectTo = '/classroom/' . request('course')->slug . '/enroll';
            Session::put('redirectTo', $redirectTo);

            return redirect()->route('register');
        }

        /**
         * @todo move this to a middleware
         */
        if (user_enrolled($request->course)) {
            return redirect($request->course->url);
        }

        if ($request->course->free) {
            if ($request->course->buy_with_points) {
                $course = $request->course;
                return view('enroll.with-points', compact('course'));
            }

            user()->courses()->attach($request->course->id);

            event(new UserEnrolled(user(), $request->course));

            return redirect($request->course->url);
        }

        $cardsResponse = $this->dispatchNow(new GetUserCards(user(), $request->course->infusionsoft->is_account));
        $relatedCourses = Course::take(1)->get(); // todo: Create the selector for these.

        $cards = [];
        if (! $cardsResponse->hasError()) {
            $cards = $cardsResponse->getResponse();
        }

        return view('enroll.index', compact('cards', 'relatedCourses'));
    }

    /**
     * @param Request $request
     * @return EnrollController|RedirectResponse
     */
    public function enroll(Request $request)
    {
        if ($request->course->free && $request->course->buy_with_points) {
            user()->courses()->attach($request->course->id);

            event(new UserEnrolled(user(), $request->course));

            return redirect($request->course->url);
        }
        
        $card = null;
        if ($request->saved_credit_card) {
            $card = json_decode($request->saved_credit_card);

            return $this->processOrder($card, $request);
        }

        $this->validateRequest($request);

        return $this->prepareOrder($request);
        
    }

    /**
     * @param \stdClass $card
     * @param           $request
     * @return $this
     */
    private function processOrder(\stdClass $card, $request)
    {
        $this->resolveInfusionsoftDetails($request);
        $course = $request->course;

        $order = $this->dispatchNow(new CreateInfusionsoftOrder($course, $card));

        if ($order->hasError()) {
            return $this->redirectBack($order->getError());
        }

        $payment = $this->dispatchNow(new PayInfusionsoftOrder($course, $card, $order->getResponse()['InvoiceId']));

        if ($payment->hasError()) {
            return $this->redirectBack($payment->getError());
        }

        $isSubscription = request('payment_type', 'full') == 'subscription';
        $productId = $isSubscription
            ? $course->infusionsoft->is_subscription_product_id
            : $course->infusionsoft->is_product_id;

        $attachData = [
            'invoice_id'             => $order->getResponse()['InvoiceId'],
            'course_infusionsoft_id' => $course->infusionsoft->id,
            'is_product_id'          => $productId,
            'paid_at'                => now(),
            'subscription_payment'   => $isSubscription,
            'payments_made'          => 1,
            'payments_required'      => $course->infusionsoft->payments_required,
        ];

        user()->courses()->attach([$course->id => $attachData]);

        event(new UserEnrolled(user(), $course));
        session()->put('recent_purchase_id', $course->id);
        $this->invalidateToken($request);
        $course->infusionsoft->rotateMerchantId();

        return redirect()->route('enroll.after-sale', $course->id);
    }

    /**
     * @param $request
     *
     * @return $this|EnrollController
     */
    private function prepareOrder(Request $request)
    {
        $contact = $this->getInfusionsoftContact($request, $request->course);

        if (method_exists($contact, 'hasError') && $contact->hasError()) {
            return $this->redirectBack($contact->getError());
        }

        $dates = explode('/', $request->card['expDate']);

        $cardData = new \stdClass();
        $cardData->type = $this->getCCTypeFromNumber($request->card['number']);
        $cardData->number = $request->card['number'];
        $cardData->expMonth = $dates[0];
        $cardData->expYear = $dates[1];
        $cardData->cvv = $request->card['cvv'];
        $cardData->nameOnCard = $request->card['nameOnCard'];

        $cardValid = $this->dispatchNow(new ValidateCard($contact, $cardData));

        if ($cardValid->hasError()) {
            return $this->redirectBack($cardValid->getError());
        }

        $card = $this->dispatchNow(new AddContactCard($contact, $cardData));

        if ($card->hasError()) {
            return $this->redirectBack($card->getError());
        }

        return $this->processOrder($this->prepareCard($card, $contact->is_contact_id), $request);
    }

    /**
     * @param Request $request
     * @param Course  $course
     *
     * @return $this|mixed
     */
    private function getInfusionsoftContact(Request $request, Course $course)
    {
        $contact = $request->user()->contactIdFor($course->infusionsoft->is_account)->first();

        if ($contact) {
            return $contact;
        }

        $response = $this->dispatchNow(
            new GetUserContactId($request->user(), $course->infusionsoft->is_account)
        );

        if ($response->hasError()) {
            return $response;
        }

        if (! $response->getResponse()) {
            $response = $this->dispatchNow(
                new CreateUserContact(
                    $request->user(),
                    $course->infusionsoft->is_account
                )
            );
        }

        if ($response->hasError()) {
            return $response;
        }

        return $response->getResponse();
    }

    private function prepareCard(AddContactCard $cardResponse, $contactId)
    {
        $cardData = new \stdClass();

        $cardData->Id = $cardResponse->getResponse();
        $cardData->ContactId = $contactId;

        return $cardData;
    }

    /**
     * @param Request $request
     */
    private function validateRequest(Request $request)
    {
        $date = preg_replace('/\s+/', '', $request->get('card')['expDate']);
        $request->replace([
            'card'   => array_merge($request->get('card'), ['expDate' => $date]),
            'course' => $request->course,
            'payment_type' => $request->payment_type
        ]);

        $this->validate($request, [
            'payment_type' => 'required',
            'card.number'  => 'required|ccn',
            'card.expDate' => 'required|ccd',
            'card.cvv'     => 'required|cvc',
        ], [
            'payment_type'     => 'Payment type is required',
            'card.number.ccn'  => 'Invalid credit card number',
            'card.cvv.cvc'     => 'Invalid CVV number',
            'card.expDate.ccd' => 'Invalid card expiry date',
        ]);
    }

    private function getCCTypeFromNumber($number)
    {
        $r = new ReflectionMethod(CreditCard::class, 'creditCardType');
        $r->setAccessible(true);
        $type = strtoupper($r->invoke(new CreditCard(), $number));
        if (! $type) {
            $type = "Unknown";
        }

        return $type;
    }

    private function resolveInfusionsoftDetails($request)
    {
        $courseUpsellToken = CourseUpsellToken::where('token', $request->token)
            ->where('used', 0)
            ->first();

        if ($courseUpsellToken) {
            $request->course->infusionsoft = $courseUpsellToken->courseUpsell->infusionsoft;
        }
    }

    private function invalidateToken($request)
    {
        if (! $request->token) {
            return;
        }

        CourseUpsellToken::where('token', $request->token)->update(['used' => 1]);
    }

    /**
     * @param $cardValid
     *
     * @return $this
     */
    private function redirectBack($error)
    {
        return redirect()->back()->withInput(['payment_type'])->withErrors($error);
    }
}

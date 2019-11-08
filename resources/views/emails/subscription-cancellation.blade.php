@component('mail::message')
# Hi {{ $subscription->user->name }},

We would like to let you know that your {{ $subscription->course->title }} subscription will be cancelled in {{ $data->remainingDays }} days if you're not able to place your monthly payment on time.

You might have received this because your credit card got rejected for a reason which you can find in your email, when an automatic re-charge was attempted.

To maintain your subscription, and pay the current invoice, please click the following link:

@component('mail::button', ['url' => $data->paymentUrl])
Click to Pay
@endcomponent

@component('mail::subcopy')
Please don't respond to this email, and forward instead this email to <a href="mailto:support@lurn.com">support@lurn.com</a> along with your response.
@endcomponent
@endcomponent

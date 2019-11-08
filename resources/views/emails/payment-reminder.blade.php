@component('mail::message')
# Hi {{ $subscription->user->name }},

The {{ $numberWord }} payment for your {{ $subscription->course->title }} subscription is scheduled to arrive in a couple of days.

@if (
    $subscription->infusionsoft->is_subscription_discount_product_url &&
    $subscription->infusionsoft->notifications_sent == 1 &&
    $subscription->payments_required == 3
)
    <p>If you wish to simply pay the remaining balance for this program and cancel the remaining payments <a href="{{ $subscription->infusionsoft->subscription_payment_url }}">click here</a></p>
@endif

The payment will continue your access to the course.

Should you have any questions about this charge, please reach out to us at <a href="mailto:support@lurn.com">support@lurn.com</a>

@component('mail::subcopy')
Please don't respond to this email, and create instead a new email to the above email address if you wish to respond.
@endcomponent
@endcomponent

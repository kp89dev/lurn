@component('mail::message')
# Hello
You've received a new email support message.

@component('mail::panel')
From: {{ $sender->name or '' }} &lt;{{ $sender->email }}&gt;
@endcomponent

@component('mail::panel')
{!! $msg !!}
@endcomponent

@component('mail::subcopy')
Please don't respond to this email, and create instead a new email to the above email address if you wish to respond.
@endcomponent
@endcomponent

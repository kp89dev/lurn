@component('mail::message')
# Hi {{ $reminder->user->name }},

We noticed that you were interested in checking out our fantastic course, {{ $reminder->course->title }}!

@component('mail::subcopy')
Please don't respond to this email, and create instead a new email to the above email address if you wish to respond.
@endcomponent
@endcomponent

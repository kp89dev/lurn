@component('mail::message')
## Hey there,

I wanted to share this with you.

I just got a free account at "Lurn Nation" and thought of you. 

It's basically a free to use online "hub" that has tons of free courses and trainings. I haven't even looked at everything yet but I saw courses there on how to start your own email business, marketing, how to use Facebook, YouTube, how to get free leads from Instagram - and they even have ecommerce training (for Shopify).

And it's 100% free.

So, I'm just passing it on to you because I thought you might get a lot of this too 🙂. You can check it out here if you'd like:

[Join the Lurn Nation invitation-only launch!]({{ $referral_link }})

Let me know if you sign up to so we can chat about it!

Cheers,

{{ $user->name }}

@component('mail::subcopy')
Please don't respond to this email, and create instead a new email to the above email address if you wish to respond.
@endcomponent
@endcomponent

@component('mail::message')
# One Last Step

Confirm Your Email to be able to create any Thread in the form, and also to ensure that You're a Human, coo!

@component('mail::button', ['url' => url('/register/confirm?token=' . $user->confirmation_token)])
Confirm Email
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

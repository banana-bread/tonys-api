@component('mail::message')
If you didn't request a password reset, you can safely ignore this email.

@component('mail::button', ['url' => $url])
Reset password
@endcomponent

@endcomponent
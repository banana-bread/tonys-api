@component('mail::message')
{{ $employee->name }} from {{ $company->name }} has invited you to create a staff account with {{ config('app.name') }}.

@component('mail::button', ['url' => $url])
Create account
@endcomponent

@endcomponent
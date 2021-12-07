@component('mail::message')

<p>Your booking with 
<span style="font-weight: bold;">{{ $company->name }}</span>  
on 
<span style="font-weight: bold;">{{ $booking->started_at->setTimezone($company->timezone)->format('D, M d, g:i A') }}</span> 
has been cancelled.</p>

@endcomponent

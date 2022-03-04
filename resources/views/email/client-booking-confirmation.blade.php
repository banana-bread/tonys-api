@component('mail::message')

@component('mail::panel')
<div>
    <h1 style="text-align: center;
               font-size: 28px;">
    Thanks for booking with <br> {{ $company->name }}!
    </h1>
</div>
@endcomponent
<br>
<div>
<!-- <p>For any cancellations, please contact us. <span style="font-weight: bold;">{{ $company->formatted_phone }}</span></p> -->
</div>

<div>
    <span>Service:</span>
    <span style="font-weight: bold;">{{ $service_names }}</span>
</div>
<div>
    <span>Duration:</span>
    <span style="font-weight: bold;">{{ $booking->formatted_duration }}</span>
</div>
<div>
    <span>Staff:</span>
    <span style="font-weight: bold;">{{ $booking->employee->first_name }}</span>
</div>
<div>
    <span>Booking time:</span>
    <span style="font-weight: bold;">{{ $booking->started_at->setTimezone($company->timezone)->format('D, M d, g:i A') }}</span> 
</div>
<div>
    <span>Total:</span>
    <span style="font-weight: bold;">{{ $booking->formatted_total }}</span>
</div>

@component('mail::button', ['url' => $bookingUrl ])
    View booking
@endcomponent

@endcomponent


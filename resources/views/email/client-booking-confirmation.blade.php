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
<p>This is a paragraph that explains certain things, like booking cancellation grace period or any restrictions related to COVID.</p>
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
    <span style="font-weight: bold;">{{ $booking->client->first_name }}</span>
</div>
<div>
    <span>Booking time:</span>
    <span style="font-weight: bold;">{{ $booking->started_at->format('D, M d, g:i A') }}</span> 
</div>
<div>
    <span>Total:</span>
    <span style="font-weight: bold;">{{ $booking->formatted_total }}</span>
</div>

@component('mail::button', ['url' => 'https://laracasts.com'])
    View your booking
@endcomponent

@endcomponent


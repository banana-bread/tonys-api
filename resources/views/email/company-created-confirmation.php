@component('mail::message')

<div style="text-align: center;">
    <h1>
    Thanks for creating a company with us
    </h1>
</div>


@component('mail::button', ['url' => 'https://laracasts.com'])
    View your booking
@endcomponent

@component('mail::table')
| Service |  Subtotal |
| ------: |----------:|
| Men's cut | $20 |
@endcomponent

@endcomponent
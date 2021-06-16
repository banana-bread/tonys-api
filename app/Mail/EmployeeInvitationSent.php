<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class EmployeeInvitationSent extends Mailable
{
    use Queueable;

    public string $url;
    // public Company $company;

    public function __construct()
    {
        // $this->company = auth()->user()->employee()->company;
        $this->url = 
            config('app.man_spa_url'). 
            //TODO: uncomment when testing for real
            // $this->company->id. 
            '6b5f38fd-462b-4c34-83a2-c9fcf3113e09'.
            '/staff/new?signed-url='.
            rawurlencode(URL::temporarySignedRoute('employee-registration', now()->addWeek(), ['companyId' => '6b5f38fd-462b-4c34-83a2-c9fcf3113e09']));
    }

    public function build()
    {
        return $this->from('adriano@example.com')
            // TODO: create template
            ->markdown('email.employee-invitation');
    }
}

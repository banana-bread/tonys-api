<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class EmployeeInvitationSent extends Mailable
{
    use Queueable;

    public string $url;
    public Employee $employee;
    public Company $company;

    public function __construct()
    {
        $this->employee = auth()->user()->employee;
        $this->company = $this->employee->company;
        
        $this->url = 
            config('app.man_spa_url'). 
            $this->company->id. 
            '/staff/new?signed-url='.
            rawurlencode(URL::temporarySignedRoute('employee-registration', now()->addWeek(), ['companyId' => '6b5f38fd-462b-4c34-83a2-c9fcf3113e09']));
    }

    public function build()
    {
        return $this->from('simplebarberapp@gmail.com', 'Simple Barber')
            ->subject('Invitation from ' . $this->company->name)
            ->markdown('email.employee-invitation', [
                'employee' => $this->employee,
                'company' => $this->company
            ]);
    }
}

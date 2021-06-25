<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeInvitationRequest;
use App\Mail\EmployeeInvitationSent;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class EmployeeInvitationController extends ApiController
{
    public function store(EmployeeInvitationRequest $request, string $companyId): JsonResponse
    {
        if (! Gate::allows('send-employee-invitation'))
        {
            throw new AuthorizationException('User not authorized');
        }

        collect(request('emails'))->each(function ($email) {
            Mail::to($email)->send(new EmployeeInvitationSent);
        });

        return $this->created(null, 'Invitations sent.');
    }
}

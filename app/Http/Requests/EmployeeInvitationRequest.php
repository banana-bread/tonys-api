<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeInvitationRequest extends FormRequest
{
    public function authorize(){return true;}

    public function rules()
    {
        return [
            'emails'   => 'required|array',
            'emails.*' => 'required|email'
        ];
    }
}

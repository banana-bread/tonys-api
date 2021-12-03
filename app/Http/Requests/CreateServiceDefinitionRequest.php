<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class CreateServiceDefinitionRequest extends FormRequest
{    
    public function rules()
    {
        return [
            'name' =>  'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric'
        ];
    }

    public function authorize(){return true;}
}

<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class CreateServiceDefinitionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        if (auth()->user()->isClient()) throw new AuthorizationException();

        $this->merge(['company_id' => auth()->user()->employee->company_id]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => 'required|uuid',
            'name' =>  'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric'
        ];
    }
}

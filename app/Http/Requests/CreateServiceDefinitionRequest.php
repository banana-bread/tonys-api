<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceDefinitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->isOwner() || auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' =>  'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric'
        ];
    }
}

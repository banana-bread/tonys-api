<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class CreateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        logger(request());
        return [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'phone'    => 'phone:CA|nullable',
            'password' => 'required|string', 
        ];
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation()
    {
        if (! $this->password) { return; }

        $this->merge([
            'password' => Hash::make($this->password)
        ]);
    }
}

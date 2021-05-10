<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteServiceDefinitionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->isOwner() || auth()->user()->isAdmin();
    }

    public function rules()
    {
        return [];
    }
}

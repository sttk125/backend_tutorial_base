<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCompleteRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'email'    => ['required','email'],
            'token'    => ['required','string','min:4','max:64'],
            'name'     => ['required','string','max:255'],
            'password' => ['required','string','min:8','max:100'],
        ];
    }
}

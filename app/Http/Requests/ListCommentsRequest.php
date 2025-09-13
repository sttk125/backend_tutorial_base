<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListCommentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; //誰でも可
    }

    public function rules(): array
    {
        return [
            'page'  => ['sometimes','integer','min:1'],
            'limit' => ['sometimes','integer','min:1','max:100'],
        ];
    }
}

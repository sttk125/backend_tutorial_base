<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可は後でPolicyで
    }

    public function rules(): array
    {
        return [
            'comment' => ['required','string','min:10','max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'comment' => 'コメント本文',
        ];
    }

    public function messages(): array
    {
        return [
            'comment.min' => 'コメントは:min文字以上で入力してください。',
            'comment.max' => 'コメントは:max文字以下で入力してください。',
        ];
    }
}

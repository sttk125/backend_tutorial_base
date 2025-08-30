<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // いまはつかわない
    }

    public function rules(): array
    {
        return [
            //'user_id' => ['required','integer','exists:users,id'],
            'comment' => ['required','string','min:10','max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            //'user_id' => 'ユーザーID',
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


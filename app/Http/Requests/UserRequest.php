<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UserRequest extends Request
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
        return [
            'email' => 'email',
            'login_id' => 'required|regex:/^[a-zA-Z0-9]+$/|between:3,16',
            'password' => 'required|regex:/^[a-zA-Z0-9]+$/|between:8,32|confirmed',
        ];
    }

    public function messages()
    {
        return [
            //'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスの形式が不正です。',
            'password.required'  => 'パスワードを入力してください。',
            'password.between'  => 'パスワードは8～32文字で入力してください。',
            'password.confirmed'  => 'パスワードとパスワード(確認用)が一致しません。',
            'password.regex'  => 'パスワードは半角英数字で入力してください。',
            'login_id.required'  => 'ユーザーIDを入力してください。',
            'login_id.between'  => 'ユーザーIDは3～16文字で入力してください。',
            'login_id.regex'  => 'ユーザーIDは半角英数字で入力してください。',

        ];
    }
}

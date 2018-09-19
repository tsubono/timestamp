<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends Request
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
            'email' => 'required|email|unique:contracts,email',
            //'domain_name' => 'required|regex:/^[0-9a-zA-Z]+$/',
            //'domain_name' => 'required|regex:/^(?!.*_).*(?=[0-9a-zA-Z]).*$/',
            'domain_name' => 'required|regex:/^[a-z0-9]+$/|unique:contracts,domain_name',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'メールアドレスが必要です。',
            'email.email' => 'メールアドレスの形式が不正です。',
            'email.unique'  => 'メールアドレスが既に登録されています。',
            'domain_name.required'  => 'サブドメイン名が必要です。',
            'domain_name.regex'  => 'サブドメイン名は半角英数字で入力してください。',
            'domain_name.unique'  => 'サブドメイン名が既に登録されています。',
        ];
    }
}

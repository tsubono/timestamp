<?php

namespace App\Http\Requests;

use Auth;

class PaymentRequest extends Request
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
            'name' => 'required',
            'number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvc' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => '名義を入力してください。',
            'number.required'  => 'カード番号を入力してください。',
            'exp_month.required'  => 'カード番号を入力してください。',
            'exp_year.required'  => '有効期限を入力してください。',
            'cvc.required'  => 'セキュリティコードを入力してください。',
        ];
    }
}

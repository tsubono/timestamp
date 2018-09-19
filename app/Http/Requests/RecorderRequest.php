<?php

namespace App\Http\Requests;


class RecorderRequest extends Request
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
            'pass_code' => 'required|regex:/^[A-Za-z0-9]*\z/|min:4',
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => '名前を入力してください。',
            'pass_code.required'  => 'パスコードを入力してください。',
            'pass_code.regex' => 'パスコードは半角英数字で入力してください。',
            'pass_code.min' => 'パスコードは4文字以上で入力してください。',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends Request
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
            'lname' => 'required',
            'fname' => 'required',
            'lname_kana' => 'required|regex:/^[ァ-ヾ]+$/u',
            'fname_kana' => 'required|regex:/^[ァ-ヾ]+$/u',
            'gender' => 'required',
            'birthday' => 'date_format:Y-m-d',
            'joined_date' => 'date_format:Y-m-d',
            'resigned_date' => 'date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'lname.required'  => '姓を入力してください。',
            'fname.required'  => '名を入力してください。',
            'lname_kana.required'  => '姓（カナ）を入力してください。',
            'fname_kana.required'  => '名（カナ）を入力してください。',
            'lname_kana.regex'  => '姓（カナ）を全角カタカナで入力してください。',
            'fname_kana.regex'  => '名（カナ）を全角カタカナで入力してください。',
            'gender.required' => '性別を選択してください。',
            'birthday.date_format' => '生年月日は2017-01-01 形式で入力してください。',
            'joined_date.date_format' => '入社日は2017-01-01 形式で入力してください。',
            'resigned_date.date_format' => '退職日は2017-01-01 形式で入力してください。',
        ];
    }
}

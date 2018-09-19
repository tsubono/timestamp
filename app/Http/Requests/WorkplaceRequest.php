<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkplaceRequest extends Request
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
            'formal_name' => 'required',
            'zip_1' => 'required|digits:3/|numeric',
            'zip_2' => 'required|digits:4/|numeric',
            'pref' => 'required',
            'address' => 'required',
            'tel' => 'required|regex:/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/',
            'timing_of_tomorrow' => 'required|date_format:H:i',
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => '略称を入力してください。',
            'formal_name.required'  => '正式名称を入力してください。',
            'zip_1.required' => '郵便番号(3桁)を入力してください。',
            'zip_1.numeric' => '郵便番号(3桁)を半角数字で入力してください。',
            'zip_1.digits' => '郵便番号(3桁)は3桁で入力してください',
            'zip_2.required' => '郵便番号(4桁)を入力してください。',
            'zip_2.numeric' => '郵便番号(4桁)を半角数字で入力してください。',
            'zip_2.digits' => '郵便番号(4桁)は4桁で入力してください。',
            'pref.required' => '都道府県を入力してください。',
            'address.required' => '市区町村・番地を入力してください。',
            'tel.required' => '電話番号を入力してください。',
            'tel.regex' => '電話番号は数字とハイフンで入力してください。',
            'timing_of_tomorrow.required' => '日付変更時刻を入力してください。',
            'timing_of_tomorrow.date_format' => '日付変更時刻は00:00形式で入力してください。',
        ];
    }
}

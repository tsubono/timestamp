<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractInfoRequest extends Request
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
            'company_name' => 'required',
            'company_name_kana' => 'required|regex:/^[\s　]*(.[ァ-ヶー\s]+$)[\s　]*$/u',
            'person_name' => 'required',
            'person_name_kana' => 'required|regex:/^[\s　]*(.[ァ-ヶー\s]+$)[\s　]*$/u',
            'zip_1' => 'required|digits:3/|numeric',
            'zip_2' => 'required|digits:4/|numeric',
            'pref' => 'required',
            'address' => 'required',
            'tel' => 'required|regex:/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required'  => '会社名を入力してください。',
            'company_name_kana.required'  => '会社名（カナ）を入力してください。',
            'company_name_kana.regex'  => '会社名（カナ）を全角カタカナで入力してください。',
            'person_name.required'  => '担当者名を入力してください。',
            'person_name_kana.required'  => '担当者名（カナ）を入力してください。',
            'person_name_kana.regex'  => '担当者名（カナ）を全角カタカナで入力してください。',
            'zip_1.required' => '郵便番号(3桁)を入力してください。',
            'zip_1.numeric' => '郵便番号(3桁)を半角数字で入力してください。',
            'zip_1.digits' => '郵便番号(3桁)は3桁で入力してください。',
            'zip_2.required' => '郵便番号(4桁)を入力してください。',
            'zip_2.numeric' => '郵便番号(4桁)を半角数字で入力してください。',
            'zip_2.digits' => '郵便番号(4桁)は4桁で入力してください。',
            'pref.required' => '都道府県を入力してください。',
            'address.required' => '市区町村・番地を入力してください。',
            'tel.required' => '電話番号を入力してください。',
            'tel.regex' => '電話番号は数字とハイフンで入力してください。',
        ];
    }
}

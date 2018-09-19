<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends Request
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
            'year' => 'required',
            'month' => 'required',
            'employee_uid' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'year.required'  => '対象年を入力してください。',
            'month.required'  => '対象月を入力してください。',
            'employee_uid.required'  => '従業員を選択してください。',
        ];
    }
}

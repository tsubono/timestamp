<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimecardRequest extends Request
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
            'employee_uid' => 'required',
            'time' => 'required|date_format:Y-m-d H:i',
        ];
    }

    public function messages()
    {
        return [
            'employee_uid.required'  => '従業員を選択してください。',
            'time.required'  => '日時を入力してください。',
            'time.date_format'  => '日時は2017-01-01 00:00 形式で入力してください。',
        ];
    }
}

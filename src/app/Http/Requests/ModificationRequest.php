<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ModificationRequest extends FormRequest
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
            'modified_punch_in' => 'required|date_format:H:i|before:modified_punch_out',
            'modified_punch_out' => 'required|date_format:H:i|after:modified_punch_in',
            'modified_break_in' => 'required|array',
            'modified_break_out' => 'required|array',
            'modified_break_in.*' => 'date_format:H:i|after:modified_punch_in|before:modified_punch_out',
            'modified_break_out.*' => 'date_format:H:i|after:modified_punch_in|before:modified_punch_out',
            'additional_break_in' => 'nullable|date_format:H:i|after:modified_punch_in|before:modified_punch_out',
            'additional_break_out' => 'nullable|date_format:H:i|before:modified_punch_out|after:modified_punch_in',
            'comment' => 'required|max:50',
        ];
    }

    public function messages()
    {
        return [
            '*.date' => '時刻は「HH:mm」形式で入力してください',

            'modified_punch_in.required' => '出勤時刻を入力してください',
            'modified_punch_out.required' => '退勤時刻を入力してください',
            'modified_punch_in.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'modified_punch_out.after' => '出勤時間もしくは退勤時間が不適切な値です',

            'modified_break_in.required' => '休憩入時刻を入力してください',
            'modified_break_out.required' => '休憩戻時刻を入力してください',
            'modified_break_in.*.before' => '休憩時間が勤務時間外です',
            'modified_break_in.*.after' => '休憩時間が勤務時間外です',
            'modified_break_out.*.before' => '休憩時間が勤務時間外です',
            'modified_break_out.*.after' => '休憩時間が勤務時間外です',
            'additional_break_*.before' => '休憩時間が勤務時間外です',
            'additional_break_*.after' => '休憩時間が勤務時間外です',

            'comment.required' => '備考を記入してください',
            'comment.max' => '備考は:max文字以下で記入してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $modIns = $this->modified_break_in;
            $modOuts = $this->modified_break_out;

            $comparisonIn = $modIns[0];
            $comparisonOut = $modOuts[0];

            $addIn = $this->additional_break_in;
            $addOut = $this->additional_break_out;

            if ($addIn xor $addOut) {
                $validator->errors()->add('additional_break_in', '休憩入と休憩戻の時刻をペアで入力してください');
            }

            if ($addIn && $addOut) {
                if ($addIn >= $addOut) {
                    $validator->errors()->add('additional_break_in', '休憩入時刻は休憩戻時刻より前である必要があります');
                }
            }

            foreach ($modIns as $index => $modIn) {
                if ($modIn >= $modOuts[$index]) {
                    $validator->errors()->add("modified_break_in.$index", '休憩入時刻は休憩戻時刻より前である必要があります');
                }

                if ($addIn < $modOuts[$index] && $addOut > $modIn) {
                    $validator->errors()->add('additional_break_in', '休憩時刻に重複があります');
                }

                if($index > 0) {
                    if ($modIn < $comparisonOut && $modOuts[$index] > $comparisonIn) {
                        $validator->errors()->add("modified_break_in.$index", '休憩時刻に重複があります');
                    }
                }

            }
        });
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

use function PHPUnit\Framework\isEmpty;

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
        $rules =  [
            'modified_punch_in' => 'required|date_format:H:i|before:modified_punch_out',
            'modified_punch_out' => 'required|date_format:H:i|after:modified_punch_in',
            'additional_break_in' => 'nullable|date_format:H:i|after_or_equal:modified_punch_in|before_or_equal:modified_punch_out',
            'additional_break_out' => 'nullable|date_format:H:i|before_or_equal:modified_punch_out|after_or_equal:modified_punch_in',
            'comment' => 'required|max:50',
        ];

        // modified_break_in の存在チェック
        if ($this->has('modified_break_in')) {
            $rules['modified_break_in'] = 'required|array';
            $rules['modified_break_in.*'] = 'required|date_format:H:i|after_or_equal:modified_punch_in|before_or_equal:modified_punch_out';
        } else {
            $rules['modified_break_in'] = 'nullable|array';
            $rules['modified_break_in.*'] = 'nullable|date_format:H:i|after_or_equal:modified_punch_in|before_or_equal:modified_punch_out';
        }

        // modified_break_out の存在チェック
        if ($this->has('modified_break_out')) {
            $rules['modified_break_out'] = 'required|array';
            $rules['modified_break_out.*'] = 'required|date_format:H:i|after_or_equal:modified_punch_in|before_or_equal:modified_punch_out';
        } else {
            $rules['modified_break_out'] = 'nullable|array';
            $rules['modified_break_out.*'] = 'nullable|date_format:H:i|after_or_equal:modified_punch_in|before_or_equal:modified_punch_out';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            '*.date_format' => '時刻は「HH:mm」形式で入力してください',

            'modified_punch_in.required' => '出勤時刻を入力してください',
            'modified_punch_out.required' => '退勤時刻を入力してください',
            'modified_punch_in.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'modified_punch_out.after' => '出勤時間もしくは退勤時間が不適切な値です',

            'modified_break_in.*.required' => '休憩開始時刻を入力してください',
            'modified_break_out.*.required' => '休憩終了時刻を入力してください',

            'modified_break_in.*.before_or_equal' => '休憩時間が勤務時間外です',
            'modified_break_in.*.after_or_equal' => '休憩時間が勤務時間外です',
            'modified_break_out.*.before_or_equal' => '休憩時間が勤務時間外です',
            'modified_break_out.*.after_or_equal' => '休憩時間が勤務時間外です',
            'additional_break_*.before_or_equal' => '休憩時間が勤務時間外です',
            'additional_break_*.after_or_equal' => '休憩時間が勤務時間外です',

            'comment.required' => '備考を記入してください',
            'comment.max' => '備考は:max文字以下で記入してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $addIn = $this->additional_break_in;
            $addOut = $this->additional_break_out;

            if ($addIn xor $addOut) {
                $validator->errors()->add('additional_break_in', '休憩入と休憩戻の時刻をペアで入力してください');
            }

            if ($addIn && $addOut) {
                if ($addIn > $addOut) {
                    $validator->errors()->add('additional_break_in', '休憩入時刻は休憩戻時刻より前である必要があります');
                }
            }

            $modIns = $this->modified_break_in;
            $modOuts = $this->modified_break_out;

            if (!($modIns && $modOuts)) {
                return;
            }

            foreach ($modIns as $index => $modIn) {
                if ($modIn > $modOuts[$index]) {
                    $validator->errors()->add("modified_break_in.$index", '休憩入時刻は休憩戻時刻より前である必要があります');
                }

                    if ($addIn < $modOuts[$index] && $addOut > $modIn) {
                        $validator->errors()->add('additional_break_in', '休憩時刻に重複があります');
                    }

                // modIns(Outs)が複数の場合、その組み合わせ全てに対して重複の判定
                if ($index > 0) {
                    for ($j = 0; $j < $index; $j++) {
                        if ($modIn < $modOuts[$j] && $modOuts[$index] > $modIns[$j]) {
                            $validator->errors()->add("modified_break_in.$index", '休憩時刻に重複があります');
                        }
                    }
                }
            }
        });
    }
}

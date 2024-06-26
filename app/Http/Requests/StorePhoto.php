<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhoto extends FormRequest
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
            // 必須入力|ファイルであること|ファイルタイプが jpg,jpeg,png,gif であること
            'photo' => 'required|file|mimes:jpg,jpeg,png,gif'
        ];
    }
}

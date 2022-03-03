<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiUploadPDFRequest extends FormRequest
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
            'title' => 'required',
            'name' => 'required',
            // 'link' => 'required',
            'file' => 'required|mimes:jpeg,png,jpg,pdf|max:10000'
        ];
    }
}

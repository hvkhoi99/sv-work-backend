<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiEducationRequest extends FormRequest
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
            'major' => 'required|min:5|max:255',
            'school' => 'required|min:5|max:255',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'achievements' => 'required|min:5'
        ];
    }
}

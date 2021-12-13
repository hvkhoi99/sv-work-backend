<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiExperienceRequest extends FormRequest
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
            'position' => 'required|max:255',
            'company' => 'required|min:5|max:255',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            // 'current_job' => '',
            'description' => 'required|min:5'
        ];
    }
}

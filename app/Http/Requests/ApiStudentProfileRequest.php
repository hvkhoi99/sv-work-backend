<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiStudentProfileRequest extends FormRequest
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
            'email' => 'required|email',
            // 'first_name' => 'nullable',
            'last_name' => 'required|max:255',
            // 'avatar_link' => '',
            'date_of_birth' => 'required|date',
            'phone_number' => 'required',
            'nationality' => 'required|max:255',
            'address' => 'required|min:5|max:255',
            'gender' => 'required',
            'over_view' => 'required|min:5|max:255',
            // 'open_for_job' => 'required',
            'job_title' => 'required|min:5|max:255'
        ];
    }
}

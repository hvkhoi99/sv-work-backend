<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRecruitmentRequest extends FormRequest
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
            'title' => 'required|min:5|max:255',
            'position' => 'required|min:5|max:255',
            'is_full_time' => 'required',
            'job_category' => 'required|min:5|max:255',
            'location' => 'required|min:5|max:255',
            'description' => 'required|min:5',
            'requirement' => 'required|min:5',
            'min_salary' => 'required|numeric',
            'max_salary' => 'required|numeric',
            'benefits' => 'required|min:5',
            'expiry_date' => 'required|date',
            'hashtags_id' => 'required'
        ];
    }
}

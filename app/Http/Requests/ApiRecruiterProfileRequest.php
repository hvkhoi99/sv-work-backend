<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRecruiterProfileRequest extends FormRequest
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
            'contact_email' => 'required|email',
            'company_name' => 'required|min:10|max:50',
            // 'logo_image_link' => 'required',
            // 'description_image_link' => 'required',
            'description' => 'required|min:10',
            'phone_number' => 'required|numeric',
            'address' => 'required|min:5|max:255',
            'company_size' => 'required|numeric',
            'company_industry' => 'required|min:5|max:255',
            'tax_code' => 'required|max:255',
        ];
    }
}

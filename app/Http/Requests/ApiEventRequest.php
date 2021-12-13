<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiEventRequest extends FormRequest
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
            'description' => 'required|min:5',
            'location' => 'required|min:5|max:255',
            'start_date' => 'required',
            'end_date' => 'required'
        ];
    }
}

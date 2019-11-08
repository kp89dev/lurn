<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SupportMessageRequest extends FormRequest
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
            'message'    => 'required|min:3',
            'user.email' => 'required|email',
        ];
    }

    public function messages()
    {
        return [
            'user.email.required' => 'Please enter a valid email address.',
            'user.email.email'    => 'Please enter a valid email address.',
        ];
    }
}

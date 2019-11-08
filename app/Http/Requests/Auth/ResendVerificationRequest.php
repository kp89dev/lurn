<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResendVerificationRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::exists('users')->where(function ($query) {
                $query->where('status', 0);
            })],
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => "This account doesn't exist or has already been confirmed.",
        ];
    }
}

<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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


    public function rules()
    {
        return [
            'image'                     => 'sometimes|image',
            'password_old'              => 'required_with:password_new',
            'password_new'              => 'sometimes|min:6',
            'password_new_confirmation' => 'required_with:old_password|same:password_new',
        ];
    }


    public function messages()
    {
        return [
            'password_old.required_with'  => 'The current password is required to change this information.',
            'password_new.min'            => 'The new password needs to be at least 6 chars long.',
            'password_new_confirmation.*' => 'You need to provide the confirmation for the password.',
        ];
    }
}

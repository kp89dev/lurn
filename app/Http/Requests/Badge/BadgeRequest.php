<?php

namespace App\Http\Requests\Badge;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'proof'   => 'required|array|min:1',
            'proof.*' => 'image|mimes:jpg,jpeg,bmp,png,gif,pdf,doc',
        ];
    }

    public function messages()
    {
        return [
            'proof.*' => 'The only allowed file types are: JPG, BMP, PNG, GIF, PDF, and DOC.',
        ];
    }
}

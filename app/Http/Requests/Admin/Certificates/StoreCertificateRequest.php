<?php

namespace App\Http\Requests\Admin\Certificates;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateRequest extends FormRequest
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
            'title' => 'required',
            'logo' => 'sometimes|image',
            'logo_style' => 'nullable',
            'border' => 'nullable|sometimes|image',
            'border_style' => 'nullable',
            'background' => 'nullable|sometimes|image',
            'sign' => 'nullable|sometimes|image',
            'sign_style' => 'nullable',
            'badge' => 'nullable|sometimes|image',
            'badge_style' => 'nullable',
            'style' => 'present',
            'body' => 'present',
        ];
    }
}

<?php
namespace App\Http\Requests\Admin\Badge;

use Illuminate\Foundation\Http\FormRequest;

class StoreBadgeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'image' => 'sometimes|image',
            'content' => 'required',
        ];
    }
}

<?php
namespace App\Http\Requests\Admin\Ad;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdRequest extends FormRequest
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
            'admin_title' => 'required',
            'image' => 'present|image',
            'hover_image' => 'nullable|sometimes|image',
            'link' => 'required',
            'location' => 'required|not_in:"none"',
            'position' => 'required|not_in:"none"'
        ];
    }
}

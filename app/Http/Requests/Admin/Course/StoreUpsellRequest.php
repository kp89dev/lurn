<?php

namespace App\Http\Requests\Admin\Course;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpsellRequest extends FormRequest
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
            'course_id' => 'required',
            'succeeds_course_id'     => 'required',
            'html'                   => 'required',
            'css'                    => 'required',
            'is_account'             => 'required|in:' . env('IS_ACCOUNTS')
        ];
    }
}

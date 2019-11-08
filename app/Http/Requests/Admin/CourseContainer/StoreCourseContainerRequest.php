<?php

namespace App\Http\Requests\Admin\CourseContainer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseContainerRequest extends FormRequest
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
            'title'       => 'required'
        ];
    }
}

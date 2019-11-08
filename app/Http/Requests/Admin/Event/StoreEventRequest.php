<?php

namespace App\Http\Requests\Admin\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'course_container_id'   => 'required|exists:course_containers,id',
            'title'                 => 'required',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date',
            'start_time'            => 'required_without:all_day|date_format:"g:i:s A"',
            'end_time'              => 'required_without:all_day|date_format:"g:i:s A"',
        ];
    }
    
}

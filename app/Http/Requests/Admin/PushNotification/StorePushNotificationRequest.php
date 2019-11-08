<?php

namespace App\Http\Requests\Admin\PushNotification;

use Illuminate\Foundation\Http\FormRequest;

class StorePushNotificationRequest extends FormRequest
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
            'admin_title'           => 'required',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date',
            'start_time'            => 'required|date_format:"g:i:s A"',
            'end_time'              => 'required|date_format:"g:i:s A"',
        ];
    }
    
}

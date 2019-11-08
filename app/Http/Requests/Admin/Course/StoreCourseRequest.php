<?php

namespace App\Http\Requests\Admin\Course;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
        $rules = [
            'title'       => 'required',
            'description' => 'required',
            'bonus_of'    => 'nullable',
        ];

        if (is_null(request('free'))) {
            if (request('subscription')) {
                $rules['is_subscription_product_id'] = 'required_if:purchasable,1';
            } else {
                $rules['is_product_id'] = 'required_if:purchasable,1';
            }

            $rules += [
                'is_account'     => 'required_if:purchasable,1|in:' . env('IS_ACCOUNTS')
            ];
        }

        return $rules;
    }

    /**
     * set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'             => 'The title field is required.',
            'description.required'       => 'The description field is required.',
            'is_product_id.required_if'  => 'The product id field is required when the course is purchasable.',
            'is_account.required_if'     => 'The account id field is required when the course is purchasable.',
        ];
    }
}

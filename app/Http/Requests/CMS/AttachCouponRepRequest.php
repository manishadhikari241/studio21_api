<?php

namespace App\Http\Requests\CMS;

use App\Http\Requests\APIFormRequest;

class AttachCouponRepRequest extends APIFormRequest
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
            'rep_id' => 'required',
            'coupon_id' => 'required',
            'compensation_type' => 'required|in:percentage,value',
            'quantity' => 'required|integer',
        ];
    }
}

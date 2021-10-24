<?php

namespace App\Http\Requests\CMS;

use App\Http\Requests\APIFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class CouponsRequest extends APIFormRequest
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
            'quantity' => 'required|integer',
            'multi' => 'required|boolean',
            'start_date' => 'required_if:multi,1|date|nullable',
            'end_date' => 'required_if:multi,1|date|nullable',
            'discount_type'=>'required|in:percentage,value'
        ];
    }
}

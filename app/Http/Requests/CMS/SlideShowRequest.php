<?php

namespace App\Http\Requests\CMS;

use App\Http\Requests\APIFormRequest;

class SlideShowRequest extends APIFormRequest
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
            'name' => 'required',
            'landscape' => 'required|image',
            'portrait' => 'required|image',
            'order' => 'required|integer'
        ];
    }
}

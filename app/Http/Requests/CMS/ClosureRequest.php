<?php

namespace App\Http\Requests\CMS;

use App\Http\Requests\APIFormRequest;

class ClosureRequest extends APIFormRequest
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
            'date' => 'required',
            'details' => 'required',
            'time_slots' => 'required|array|min:1',
            'time_slots.*' => 'required|int',
        ];
    }

    public function messages()
    {
        return [
            'time_slots.*.required' => 'Please select time Slots'
        ];
    }
}

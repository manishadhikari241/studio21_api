<?php

namespace App\Http\Requests\CMS;

use App\Http\Requests\APIFormRequest;

class WeeklyClosureRequest extends APIFormRequest
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
            'from' => 'required|date',
            'to' => 'required|date',
            'name' => 'required',
            'details' => 'required',
            'week_day' => 'required',
            'week_value' => 'required',
            'time_slots' => 'required|int',
        ];
    }
}

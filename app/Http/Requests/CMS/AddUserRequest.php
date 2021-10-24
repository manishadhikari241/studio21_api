<?php

namespace App\Http\Requests\CMS;

use App\Constants\Roles;
use App\Http\Requests\APIFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends APIFormRequest
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
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|string|email|confirmed|max:100|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|in:1,2,3',
            'discount_percentage' => 'required_if:role_id,' . Roles::REPRESENTATIVE

        ];
    }

    public function messages()
    {
        return [
            'discount_percentage.required_if' => 'Please provide discount percentage for Representative'
        ];
    }
}

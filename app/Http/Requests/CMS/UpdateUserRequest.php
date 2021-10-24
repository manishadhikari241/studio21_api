<?php

namespace App\Http\Requests\CMS;

use App\Constants\Roles;
use App\Http\Requests\APIFormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends APIFormRequest
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
            'email' => 'required|string|email|confirmed|max:100|' . Rule::unique('users', 'email')->ignore($this->user),
            'phone' => 'required|' . Rule::unique('users', 'phone')->ignore($this->user),
            'password' => 'nullable|string|min:6',
            'role_id' => 'required',
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

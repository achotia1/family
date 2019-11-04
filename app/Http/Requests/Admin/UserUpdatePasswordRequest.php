<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdatePasswordRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
                'old_password'  => 'required',
                'password'  => 'required|min:6',
                'confirm_password'  => 'required|same:password',
            ];
    }

    public function messages()
    {
        return [

            'old_password.required' => 'Old password field is required.',
            
            'password.required'     => 'New password field is required.',
            'password.min'          => 'New password field should be minimun 6 characters long.',

            'confirm_password.required' => __('admin.ERR_CONFIRM_PASS'),
            'confirm_password.same' => 'Confirm password don\'t match with New password.'
        ];
    }
}

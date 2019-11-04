<?php

namespace App\Http\Requests\Admin\Auth;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

   public function rules()
    {       
            return [               
                'username'  => 'required',
                'password'  => 'required'
            ];
       
    }

    public function messages()
    {
        return [
            'username.required'      => __('admin.ERR_USERNAME_REQUIRED'),
            'password.required'      => __('admin.ERR_PASSWORD_REQUIRED'),
        ];
    }
}
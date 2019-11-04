<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;


class ForgotPasswordRequest extends FormRequest
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
            return [               
                'username'          => 'required'
            ];
       
    }

    public function messages()
    {
        return [            
            'username.required'         => __('admin.ERR_USERNAME_REQUIRED'),
        ];
    }
}
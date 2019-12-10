<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // print_r($this->route('user'));
        // exit;
        $id = base64_decode(base64_decode($this->route('user'))) ?? null;
        if ($id === null) 
        {
            return [
                'name'     => 'required|regex:/^[a-zA-Z0-9\s]+$/u',
                'email'     => 'required|unique:store_users,email',
                'username'     => 'required|unique:store_users,username',
                'password'  => 'required|min:6',
                'confirm_password'  => 'required|same:password',
                'role'      => 'required',
            ];
        }
        else
        {
            return [
                'name'     => 'required|regex:/^[a-zA-Z0-9\s]+$/u',
                'email'     => 'required|unique:store_users,email,'.$id,
                'username'     => 'required|unique:store_users,username,'.$id,
                'password'  => 'nullable|min:6',
                'confirm_password'  => 'same:password'
            ];
        }
    }

    public function messages()
    {
        return [

            'name.required' => 'Name field is required.',            
            'name.regex'    => 'Name field should be in letter\'s and number\'s only.',            
            // 'company_id.required'   => 'Company Name field is required.',     

            'email.required'        => __('admin.ERR_EMAIL_NAME'),
            'email.email'           => __('admin.ERR_EMAIL_FORMAT'),
            'email.unique'          => __('admin.ERR_EMAIL_DUP'),   
            'password.required'     => __('admin.ERR_PASS'),
            'password.min'          => __('admin.ERR_PASS_MIN_SIZE'),

            'confirm_password.required' => __('admin.ERR_CONFIRM_PASS'),
            'confirm_password.same' => __('admin.ERR_COMPARE_PASS'),

            'role.required'         => __('admin.ERR_ROLE'),
        ];
    }
}

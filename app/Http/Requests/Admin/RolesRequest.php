<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RolesRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        $id = base64_decode(base64_decode($this->route('endID'))) ?? null; 
        // dd($this->route('endID'),$id,$request->all());
        if ($id === null || $id=="") 
        {
            return [
                'name'=> 'required|regex:/^[a-zA-Z\-]+$/u|unique:roles,name'
            ];
        }else{
             return [
                'name'=> 'required|regex:/^[a-zA-Z\-]+$/u|unique:roles,name,'.$id,
                //'code'     => 'required|unique:products,code,'.$id,
            ];
        }
    }

    public function messages()
    {
        return [
            'name.regex'   => 'Role name should accept latter\'s and \'-\' only.',
            'name.unique'   => 'Role name has already taken.',
            'name.required'   => 'Role name field is required.'
        ];
    }
}


// public function rules()
//     {
//         'role' => 'required|unique:roles,name'
//     }

//     public function messages()
//     {
//         return [

//             'role.unique' => 'Role name already taken.',
//             'role.required' => 'Role name field is required.',
//         ];
//     }
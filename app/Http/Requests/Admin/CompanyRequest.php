<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // print_r($this->route('faculty'));
        // exit;

        $id = base64_decode(base64_decode($this->route('company'))) ?? null;   
        if ($id === null) 
        {
           return [
                'name'     => 'required',
                //'code'     => 'required',
            ];
        }else{
             return [
                'name'     => 'required',
                //'code'     => 'required|unique:products,code,'.$id,
            ];
        }
            
     
    }

    public function messages()
    {
        return [

            'name.required'    => 'Name field is required.',            
            'name.regex'       => 'Name field should be in letter\'s and number\'s only.',            
            // 'code.required'    => 'Code field is required.',            
            // 'code.unique'      => 'Code has already taken.',
        ];
    }
}

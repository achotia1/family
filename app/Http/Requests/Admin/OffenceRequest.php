<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OffenceRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // print_r($this->route('faculty'));
        // exit;

        // $id = base64_decode(base64_decode($this->route('offence'))) ?? null;   
        /*if ($id === null) 
        {*/
           return [
                'name'     => 'required',
                'description'     => 'required',
                'penalty'     => 'required',
            ];
       /* }else{
             return [
                'name'     => 'required',
                'code'     => 'required|unique:products,code,'.$id,
            ];
        }*/
            
     
    }

    public function messages()
    {
        return [

            'name.required'         => __('admin.ERR_OFFENCE_NAME'),            
            'description.required'  => __('admin.ERR_OFFENCE_DESC'),
            'penalty.required'      => __('admin.ERR_OFFENCE_PENALTY'),
        ];
    }
}

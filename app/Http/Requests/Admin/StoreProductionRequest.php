<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {        

        $id = base64_decode(base64_decode($this->route('production'))) ?? null;        
        if ($id == null) 
        {           
           return [                
                'batch_no'     => 'required',
                'material_id'     => 'required',
                'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',                         
            ];
        }else{
             return [                
                'batch_no'     => 'required',
                'material_id'     => 'required',
                'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',                      
            ];
        }            
     
    }

    public function messages()
    {
        return [

            'batch_no.required'    => 'Batch Card Number field is required.',
            'material_id.required'    => 'Material field is required.', 
            'quantity.required'    => 'Batch Quantity field is required.',
            'quantity.regex'    => 'Quantity should be correct.',        
        ];
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnedMaterialRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // print_r($this->route('faculty'));
        // exit;

        $id = base64_decode(base64_decode($this->route('return'))) ?? null;   
        if ($id == null) 
        {
           return [                
                'plan_id'     => 'required',
                'return_date'     => 'required',
               // 'material_id'     => 'required',
                //'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                //'bill_number'     => 'required',                                
            ];
        }else{
             return [                
                'plan_id'     => 'required',
                'return_date'     => 'required',
                // 'material_id'     => 'required',
                // 'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                // 'quantity.regex'    => 'Quantity should be correct.',
                // 'bill_number'     => 'required',                               
            ];
        }          
     
    }

    public function messages()
    {
        return [

            // 'name.required'    => 'Name field is required.',            
            // 'name.regex'       => 'Name field should be in latter\'s and number\'s only.', 
            'plan_id.required'    => 'Batch Card Number field is required.',           
            'return_date.required'    => 'Return Date field is required.',            
            // 'material_id.required'    => 'Raw Material field is required.',            
            // 'quantity.required'    => 'Quantity field is required.',            
            // 'bill_number.required'    => 'Bill number field is required.',
        ];
    }
}

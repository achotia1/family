<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssuedMaterialRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // print_r($this->route('faculty'));
        // exit;

        $id = base64_decode(base64_decode($this->route('sale'))) ?? null;   
        if ($id == null) 
        {
           return [                
                'batch_no'     => 'required',
                'material_id'     => 'required',
                'issue_date'     => 'required',
                'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'bill_number'     => 'required',                                
            ];
        }else{
             return [                
                'batch_no'     => 'required',
                'material_id'     => 'required',
                'issue_date'     => 'required',
                'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'bill_number'     => 'required',                               
            ];
        }          
     
    }

    public function messages()
    {
        return [
            
            'batch_no.required'    => 'Batch Card Number field is required.',           
            'material_id.required'    => 'Raw Material field is required.',            
            'issue_date.required'    => 'Issue Date field is required.',            
            'quantity.required'    => 'Quantity field is required.',
            'quantity.regex'    => 'Quantity should be correct.',           
            'bill_number.required'    => 'Bill number field is required.',
        ];
    }
}

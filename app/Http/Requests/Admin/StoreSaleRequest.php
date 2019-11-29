<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // print_r($this->route('faculty'));
        // exit;

        //$id = base64_decode(base64_decode($this->route('return'))) ?? null;   
        // if ($id == null) 
        // {
           return [                
                'invoice_no'     => 'required',
                'invoice_date'     => 'required',
               // 'material_id'     => 'required',
                //'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                //'bill_number'     => 'required',                                
            ];
         // }
         //   else{
        //      return [                
        //         'invoice_no'     => 'required',
        //         'invoice_date'     => 'required',
        //     ];
        // }          
     
    }

    public function messages()
    {
        return [

            'invoice_no.required'    => 'Invoice Number field is required.',           
            'invoice_date.required'    => 'Invoice Date field is required.',            
            // 'name.required'    => 'Name field is required.',            
            // 'name.regex'       => 'Name field should be in latter\'s and number\'s only.', 
            // 'material_id.required'    => 'Raw Material field is required.',            
            // 'quantity.required'    => 'Quantity field is required.',            
            // 'bill_number.required'    => 'Bill number field is required.',
        ];
    }
}

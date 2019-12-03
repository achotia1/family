<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnedSaleRequest extends FormRequest
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
                'sale_invoice_id'     => 'required',
                'customer_id'     => 'required',
                'return_date'     => 'required',
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
            'sale_invoice_id.required'    => 'Invoice Number field is required.',           
            'customer_id.required'    => 'Customer Name field is required.',           
            'return_date.required'    => 'Invoice Returned Date field is required.',            
        ];
    }
}

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
        $id = base64_decode(base64_decode($this->route('sale'))) ?? null;   
        if ($id == null) 
        {
           return [                
                'invoice_no'     => 'required|unique:store_sale_invoice,invoice_no,NULL,id,deleted_at,NULL',
                'invoice_date'     => 'required',
                //'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            ];
         }
           else{
             return [                
                'invoice_no'     => 'required|unique:store_sale_invoice,invoice_no,'.$id.',id,deleted_at,NULL',
                'invoice_date'     => 'required',
            ];
        }          
     
    }

    public function messages()
    {
        return [

            'invoice_no.required'    => 'Invoice Number field is required.',           
            'invoice_date.required'    => 'Invoice Date field is required.',            
        ];
    }
}

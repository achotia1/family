<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchCardRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {        

        $id = base64_decode(base64_decode($this->route('rms_store'))) ?? null;        
        if ($id == null) 
        {           
           return [                
                'product_code'     => 'required',
                'batch_card_no'     => 'required|unique:store_batch_cards,batch_card_no',
                'batch_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            ];
        }else{
             return [                
                'product_code'     => 'required',
                'batch_card_no'     => 'required|unique:store_batch_cards,batch_card_no,'.$id,
                'batch_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',     
            ];
        }            
     
    }

    public function messages()
    {
        return [           
            'product_code.required'    => 'Product Code field is required.',
            'batch_card_no.required'    => 'Batch Code field is required.',
            'batch_card_no.unique'    =>'This Batch Code has already been taken.',
            'batch_qty.required'    => 'Batch Quantity field is required.',
            'batch_qty.regex'    => 'Batch Quantity should be correct.',
        ];
    }
}
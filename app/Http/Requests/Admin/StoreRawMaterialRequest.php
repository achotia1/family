<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRawMaterialRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {       
        $id = base64_decode(base64_decode($this->route('material'))) ?? null;   
        if ($id == null) 
        {
           return [                 
                'name'     => 'required|unique:store_raw_materials,name,deleted_at,NULL',                
                'moq'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                /*'balance_stock'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',*/
            ];
        }else{
             return [                
                'name'     => 'required|unique:store_raw_materials,name,'.$id,
                'moq'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                /*'balance_stock'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',*/
            ];
        }     
    }
    public function messages()
    {
        return [            
            'name.required'    => 'Raw Material field is required.',
            'name.unique'    => 'Raw Material name should be unique.',
            'moq.required'    => 'Minimum Order Quantity field is required.',
            'moq.regex'    => 'Minimum Order Quantity should be correct.',
            /*'balance_stock.required'    => 'Balance Stock field is required.',
            'balance_stock.regex'    => 'Balance Stock should be correct.',*/
        ];
    }
}
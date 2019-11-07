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
        //print_r($this->route('faculty'));
        echo "innn req";
        exit;
        $id = base64_decode(base64_decode($this->route('material'))) ?? null;   
        if ($id == null) 
        {
           return [                
                'name'     => 'required|unique:store_raw_materials,name',
                'unit'     => 'required',
                'price_per_unit'     => 'required',
                'opening_stock'     => 'required',
                'balance_stock'     => 'required',
                'moq'     => 'required',
                'trigger_qty'     => 'required',                
            ];
        }else{
             return [                
                'name'     => 'required|unique:store_raw_materials,name,'.$id,
                'unit'     => 'required',
                'price_per_unit'     => 'required',
                'opening_stock'     => 'required',
                'balance_stock'     => 'required',
                'moq'     => 'required',
                'trigger_qty'     => 'required',                
            ];
        }            
     
    }

    public function messages()
    {
        return [            
            'name.required'    => 'Raw Material field is required.',            
            'available_qty.required'    => 'Available Quantity field is required.'
            'moq.required'    => 'Material Order Quantity field is required.',                        
            'trigger_qty.required'    => 'Trigger Quantity field is required.',
        ];
    }
}
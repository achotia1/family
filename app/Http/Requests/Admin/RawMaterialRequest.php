<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RawMaterialRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        //print_r($this->route('faculty'));        
        //exit;
        $id = base64_decode(base64_decode($this->route('material'))) ?? null;   
        if ($id == null) 
        {
           return [           		
                'name'     => 'required|unique:store_raw_materials,name',       
                'price_per_unit'     => 'required|regex:/^\d+(\.\d{0,2})?$/u',
                'opening_stock'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'balance_stock'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'moq'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'trigger_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',                
            ];
        }else{
             return [                
                'name'     => 'required|unique:store_raw_materials,name,'.$id, 
                'price_per_unit'     => 'required|regex:/^\d+(\.\d{0,2})?$/u',
                'opening_stock'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'balance_stock'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'moq'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'trigger_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',              
            ];
        }     
    }
    public function messages()
    {
        return [            
            'name.required'    => 'Raw Material field is required.',
            'name.unique'    => 'Raw Material name should be unique.',            
            'price_per_unit.required'    => 'Price Per Unit field is required.',
            'price_per_unit.regex'    => 'Price Per Unit should be correct.',
            'opening_stock.required'    => 'Opening Stock field is required.',
            'opening_stock.regex'    => 'Opening Stock should be correct.',
            'balance_stock.required'    => 'Balance Stock field is required.',
            'balance_stock.regex'    => 'Balance Stock should be correct.',
            'moq.required'    => 'Material Order Quantity field is required.',
            'moq.regex'    => 'Material Order Quantity should be correct.',   
            'trigger_qty.required'    => 'Trigger Quantity field is required.',
            'trigger_qty.regex'    => 'Trigger Quantity should be correct.',
        ];
    }
}
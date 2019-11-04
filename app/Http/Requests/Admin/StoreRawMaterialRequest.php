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
        // print_r($this->route('faculty'));
        // exit;

        $id = base64_decode(base64_decode($this->route('material'))) ?? null;   
        if ($id == null) 
        {
           return [
                // 'name'     => 'required|unique:vehicles,name',
                'name'     => 'required|unique:store_raw_materials,name',
                'total_qty'     => 'required',
                'unit'     => 'required',
                'price_per_unit'     => 'required',
                'opening_stock'     => 'required',
                'balance_stock'     => 'required',
                'trigger_qty'     => 'required',                
            ];
        }else{
             return [
                // 'name'     => 'required|unique:vehicles,name,'.$id,
                'name'     => 'required|unique:store_raw_materials,name,'.$id,
                'total_qty'     => 'required',
                'unit'     => 'required',
                'price_per_unit'     => 'required',
                'opening_stock'     => 'required',
                'balance_stock'     => 'required',
                'trigger_qty'     => 'required',                
            ];
        }
            
     
    }

    public function messages()
    {
        return [

            // 'name.required'    => 'Name field is required.',            
            // 'name.regex'       => 'Name field should be in latter\'s and number\'s only.',            
            'name.required'    => 'Raw Material field is required.',            
            'total_qty.required'    => 'Total Quantity field is required.',            
            'available_qty.required'    => 'Available Quantity field is required.',            
            'trigger_qty.required'    => 'Trigger Quantity field is required.',
        ];
    }
}

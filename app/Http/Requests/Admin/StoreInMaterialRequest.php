<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInMaterialRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {       
        /*print($this->route('{materials_in'));
        exit;*/
        $id = base64_decode(base64_decode($this->route('materials_in'))) ?? null;
          
        if ($id == null) 
        {
           return [                 
                'material_id'     => 'required',
                'lot_no'     => 'required|unique:store_in_materials,lot_no',
                'lot_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'price_per_unit'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            ];
        }else{
             return [                
                'material_id'     => 'required',
                'lot_no'     => 'required|unique:store_in_materials,lot_no,'.$id,
                'lot_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'price_per_unit'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            ];
        }     
    }
    public function messages()
    {
        return [            
            'material_id.required'    => 'Raw Material field is required.',
            'lot_no.required'    => 'Lot Number field is required.',
            'lot_no.unique'    =>'Lot Number has already been taken.',            
            'lot_qty.required'    => 'Lot Quantity field is required.',
            'lot_qty.regex'    => 'Lot Quantity should be correct.',
            'price_per_unit.required'    => 'Price Per Unit field is required.',
            'price_per_unit.regex'    => 'Price Per Unit should be correct.',
        ];
    }
}
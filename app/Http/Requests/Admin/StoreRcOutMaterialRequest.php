<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRcOutMaterialRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {       
        /*print($this->route('{materials_in'));
        exit;*/            
        $id = base64_decode(base64_decode($this->route('materials_out'))) ?? null;
        if ($id == null) 
        {
           return [                 
                'plan_id'     => 'required',
                'sellable_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'loose_material'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'unfiltered'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'rejection'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            ];
        }else{
             return [                
                'plan_id'     => 'required',
                'sellable_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'loose_material'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'unfiltered'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'rejection'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            ];
        }     
    }
    public function messages()
    {
        return [            
            'plan_id.required'    => 'Batch Code field is required.',
            'sellable_qty.required'    => 'Sellable Quantity field is required.',
            'sellable_qty.regex'    =>'Sellable Quantity should be correct.',
            'loose_material.required'    => 'Loose Material field is required.',
            'loose_material.regex'    => 'Loose Material should be correct.',            
            'unfiltered.required'    => 'Unfiltered field is required.',
            'unfiltered.regex'    => 'Unfiltered Material should be correct.',
            'rejection.required'    => 'Water of Rejection field is required.',
            'rejection.regex'    => 'Water of Rejection should be correct.',
        ];
    }
}
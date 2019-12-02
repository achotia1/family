<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutMaterialRequest extends FormRequest
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
                'course_powder'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'rejection'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'dust_product'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'loose_material'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                                
            ];
        }else{
             return [                
                'plan_id'     => 'required',
                'sellable_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'course_powder'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'rejection'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'dust_product'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'loose_material'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                
            ];
        }     
    }
    public function messages()
    {
        return [            
            'plan_id.required'    => 'Batch Code field is required.',
            'sellable_qty.required'    => 'Sellable Quantity field is required.',
            'sellable_qty.regex'    =>'Sellable Quantity should be correct.',
            'course_powder.required'    => 'Course Powder field is required.',
            'course_powder.regex'    => 'Course Powder should be correct.',
            'rejection.required'    => 'Rejection field is required.',
            'rejection.regex'    => 'Rejection should be correct.',
            'dust_product.required'    => 'Dust Product field is required.',
            'dust_product.regex'    => 'Dust Product should be correct.',
            'loose_material.required'    => 'Loose Material field is required.',
            'loose_material.regex'    => 'Loose Material should be correct.',
        ];
    }
}
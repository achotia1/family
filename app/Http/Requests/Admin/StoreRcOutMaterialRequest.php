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
       return [                 
            'plan_id'     => 'required',
            'sellable_qty'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            'loose_material'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            'course_powder'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            'rejection'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
        ];
    }
    public function messages()
    {
        return [            
            'plan_id.required'    => 'Batch Code field is required.',
            'sellable_qty.required'    => 'Sellable Quantity field is required.',
            'sellable_qty.regex'    =>'Sellable Quantity should be correct.',
            'loose_material.required'    => 'Loose Material field is required.',
            'loose_material.regex'    => 'Loose Material should be correct.',            
            'course_powder.required'    => 'Unfiltered field is required.',
            'course_powder.regex'    => 'Unfiltered Material should be correct.',
            'rejection.required'    => 'Water of Rejection field is required.',
            'rejection.regex'    => 'Water of Rejection should be correct.',
        ];
    }
}
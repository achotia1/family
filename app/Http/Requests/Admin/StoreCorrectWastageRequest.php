<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectWastageRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    { 
           return [ 
                'corrected_cbalance'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'corrected_rbalance'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'corrected_dbalance'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
                'corrected_lbalance'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            ];
        
    }
    public function messages()
    {
        return [
            'corrected_cbalance.required'    => 'Corrected Course Balance field is required.',
            'corrected_cbalance.regex'    => 'Corrected Course Balance should be correct.',
            'corrected_rbalance.required'    => 'Corrected Rejection Balance field is required.',
            'corrected_rbalance.regex'    => 'Corrected Rejection Balance should be correct.',
            'corrected_dbalance.required'    => 'Corrected Dust Balance field is required.',
            'corrected_dbalance.regex'    => 'Corrected Dust Balance should be correct.',
            'corrected_lbalance.required'    => 'Corrected Loose Balance field is required.',
            'corrected_lbalance.regex'    => 'Corrected Loose Balance should be correct.',
        ];
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectMaterialRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    { 
           return [ 
                'corrected_balance'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
            ];
        
    }
    public function messages()
    {
        return [
            'corrected_balance.required'    => 'Corrected Balance field is required.',
            'corrected_balance.regex'    => 'Corrected Balance should be correct.',
        ];
    }
}
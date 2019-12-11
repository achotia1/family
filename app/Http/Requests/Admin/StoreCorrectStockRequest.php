<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectStockRequest extends FormRequest
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
            'corrected_balance.required'    => 'Corrected Stock Balance field is required.',
            'corrected_balance.regex'    => 'Corrected Stock Balance should be correct.',
        ];
    }
}
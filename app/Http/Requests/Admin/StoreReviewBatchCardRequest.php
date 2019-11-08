<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewBatchCardRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {		
        $id = base64_decode(base64_decode($this->route('rms_store'))) ?? null;    
        return [                
                'sell_cost'     => 'required|regex:/^\d+(\.\d{0,4})?$/u', 
        ];     
    }

    public function messages()
    {
        return [          
            'sell_cost.required'    => 'Sell Cost field is required.',
            'sell_cost.regex'    => 'Sell Cost should be correct.',
        ];
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpeningStockRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

	public function rules()
	{          
		return [                
			'product_code'     => 'required',
			'batch_card_no'  => 'required|unique:store_batch_cards,batch_card_no,NULL,id,deleted_at,NULL',        
			'quantity'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
			'manufacturing_cost'     => 'required|regex:/^\d+(\.\d{0,4})?$/u',
		];
	}

    public function messages()
    {
        return [           
            'product_code.required'    => 'Product Code field is required.',
            'batch_card_no.required'    => 'Batch Code field is required.',
            'batch_card_no.unique'    =>'This Batch Code has already been taken.',
            'quantity.required'    => 'Stock Quantity field is required.',
            'quantity.regex'    => 'Stock Quantity should be correct.',
            'manufacturing_cost.required'    => 'Manufacturing Cost field is required.',
            'manufacturing_cost.regex'    => 'Manufacturing Cost should be correct.',
        ];
    }
}
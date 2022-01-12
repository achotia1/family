<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FamilyHeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {       
        $arrValidation = [                
            'first_name'        => 'required',
            'last_name'         => 'required',
            'birth_date'        => 'required',
            'mobile_number'     => 'required',
            'address'           => 'required',
            'pincode'           => 'required',
            'state_id'          => 'required',
            'city_id'           => 'required'
        ];

        if( !empty($request->martial_status) && $request->martial_status == 1) {
            $arrValidation['wedding_date'] = 'required';
        }

        return $arrValidation;
    }
    public function messages()
    {
        return [            
            'first_name.required'       => 'First name field is required.',
            'last_name.required'        => 'Last name field is required.',
            'birth_date.required'       => 'Birthdate field is required.',            
            'mobile_number.required'    => 'Mobile Number field is required.',
            'address.required'          => 'Address should be correct.',
            'pincode.required'          => 'Pincode field is required.',
            'state_id.required'         => 'State field is required..',
            'city_id.required'          => 'City field is required.',
            'wedding_date.required'     => 'Wedding Date field is required.',
            
        ];
    }
}
<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class FamilyMemberRequest extends FormRequest
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
            'birth_date'        => 'required'
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
            'wedding_date.required'     => 'Wedding Date field is required.'            
            
        ];
    }
}
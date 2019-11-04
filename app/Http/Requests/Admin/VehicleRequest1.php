<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // print_r($this->route('faculty'));
        // exit;

        $id = base64_decode(base64_decode($this->route('vehicle'))) ?? null;   
        if ($id === null) 
        {
           return [
                // 'name'     => 'required|unique:vehicles,name',
                'chassis_number'     => 'required',
                'registration_number'     => 'required',
                'ol_number'     => 'required',
                'capacity'     => 'required',
                'ol_status'     => 'required',
                'id_number'     => 'required',
                'issue_number'     => 'required',
                // 'permit_start_date'     => 'required',
                // 'permit_end_date'     => 'required',
                'type'     => 'required',
            ];
        }else{
             return [
                // 'name'     => 'required|unique:vehicles,name,'.$id,
                'chassis_number'     => 'required',
                'registration_number'     => 'required',
                'ol_number'     => 'required',
                'capacity'     => 'required',
                'ol_status'     => 'required',
                'id_number'     => 'required',
                'issue_number'     => 'required',
                // 'permit_start_date'     => 'required',
                // 'permit_end_date'     => 'required',
                'type'     => 'required',
            ];
        }
            
     
    }

    public function messages()
    {
        return [

            // 'name.required'    => 'Name field is required.',            
            // 'name.regex'       => 'Name field should be in latter\'s and number\'s only.',            
            'chassis_number.required'    => 'Chassis Number field is required.',            
            'registration_number.required'    => 'Registration Number field is required.',            
            'ol_number.required'    => 'OL Number field is required.',            
            'capacity.required'    => 'Capacity field is required.',            
            'ol_status.required'    => 'OL Status field is required.',            
            'id_number.required'    => 'Id Number field is required.',            
            'issue_number.required'    => 'Issue Number field is required.',            
        ];
    }
}

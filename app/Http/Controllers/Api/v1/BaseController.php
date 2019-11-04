<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Hash;
use App\User;

class BaseController extends Controller {
	public static function checkValidRequest($data)
    {        
        $return  = false;
        $arrUserDetails = User::where('id', $data['user_id'])->where('status',config('constants.STATUS_ACTIVE'))->first();
        if(!empty($arrUserDetails)) {
        	if(Hash::check($data['api_access_token'], $arrUserDetails->api_access_token)){
        		$return  = true;
        	}
        } 
        return $return;     
    }
}

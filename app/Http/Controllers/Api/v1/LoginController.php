<?php

namespace App\Http\Controllers\Api\v1;

//use App\Http\Controllers\Controller\Api\v1;
//use App\Http\Controllers\Controller;
use Hash, Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use DB;

class LoginController extends BaseController
{
    /**
	 * @api {post} /sendOtp App sendOtp
	 * @apiDescription Handle a send OTP request to the mobile application.
	 * @apiGroup Webservices
	 *
	 * @apiVersion 0.1.0
	 * @apiParam {String} mobile Mobile.	 
	 * @apiParam {String} device_type Device Type ios or android.
	 * 
	 */
    public function sendOtp(Request $request)
    {
    	$flag  = true;
    	$code = config('constants.UNSUCCESS');
    	$status = config('constants.STATUS_UNSUCCESS');
        $msg = '';
        $response_data = (object)[];
        $data = $request->all();
    	$mobile = $request->input('mobile');    	
    	
    	## FIELD VALIDATIONS
		$validator = Validator::make($data, 
			[
			  'mobile'	=> 'required',		  
			  'device_type'	=> 'required'
			],
			[
			  'mobile.required'	=> __('api.MSG_MOBILE_REQ'),
			  'device_type.required' => __('api.MSG_DEVICE_TYPE_REQ'),			  
			],
		);

		## THROUGH ERROR IF VALIDATION FAILS
		if ($validator->fails()) {
            $msg = $validator->errors()->first();
            $flag = false;
        }

        if($flag) {
        	## CHECK IF MOBILE NUMBER IS VALID
        	//DB::enableQueryLog();
    		$arrUserDetails = User::where('mobile_number', $mobile)->first();  		
    		//dd(DB::getQueryLog());
    		if(!empty($arrUserDetails)) {
    			if($arrUserDetails->status==config('constants.STATUS_ACTIVE'))
    			{
    				$code = config('constants.SUCCESS');
    				$status = config('constants.STATUS_SUCCESS');    				
    				$msg = __('api.MSG_VERIFIED');

    				## GENERATE OTP CODE
    				$otp_code = rand(1000, 9999);
    				$otp_create_time = Carbon::now();//date("Y-m-d H:i:s");
    				$user = User::where('id','=',$arrUserDetails['id'])->update(['login_otp' => $otp_code, 'otp_create_time'=>$otp_create_time]);
    				$response = array();		    		
		    		$response['user_detail'] = [
                                'user_id' => $arrUserDetails->id,
                                'otp' => $otp_code,                            
                            ];
		    		$response_data = $response;
				} else {
					## IF USER IS NOT ACTIVE
					$code = config('constants.SUCCESS');					
					$msg = __('api.MSG_INACTIVE_USER');
				}    			
    			
			} else {
				## IF USER IS NOT VALID
				$code = config('constants.SUCCESS');				
				$msg = __('api.MSG_INVALID_USER');			 
			}
        }        
    	## RETURN JSON RESPONSE
    	return response()->json(['status'=>$status, 'code' => $code, 'message' => $msg, 'data' => $response_data]);
    }

    /**
	 * @api {post} /login App Login
	 * @apiDescription Handle a login request to the mobile application.
	 * @apiGroup Webservices
	 *
	 * @apiVersion 0.1.0
	 * @apiParam {String} user_id User Id.
	 * @apiParam {String} otp OTP.	 
	 * @apiParam {String} device_type Device Type ios or android.
	 * 
	 */
    public function loginWithOtp(Request $request)
    {
    	$flag  = true;
    	$code = config('constants.UNSUCCESS');
    	$status = config('constants.STATUS_UNSUCCESS');
        $msg = '';
        $response_data = (object)[];
    	$data = $request->all();    	
    	
    	## FIELD VALIDATIONS
		$validator = Validator::make($data, 
			[
			  'user_id'	=> 'required',
			  'otp'	=> 'required',	//'required|numeric'	  
			  'device_type'	=> 'required'
			],
			[
			  'user_id.required'	=> __('api.MSG_USER_ID_REQ'),
			  'device_type.required' => __('api.MSG_DEVICE_TYPE_REQ'),			  
			],
		);

		## THROUGH ERROR IF VALIDATION FAILS
		if ($validator->fails()) {
            $msg = $validator->errors()->first();
            $flag = false;
        }
        if($flag) {        	
        	## CHECK IF MOBILE NUMBER IS VALID    		
    		$arrUserDetails = User::where('id', $data['user_id'])->first();
    		
    		if($arrUserDetails) {
                if($data['otp'] == $arrUserDetails->login_otp) {
                	## RESET OTP TO NULL AFTER USE
                	## ASH User::where('id','=',$data['user_id'])->update(['login_otp' => null]);
                	
                	## CHECK IF OTP IS NOT EXPIRED                	
                	if (Carbon::parse($arrUserDetails->otp_create_time)->addMinutes(config('constants.OTP_VALID_MINS')) > Carbon::now()) {
	                	
	                	## CREATE THE ACCESS TOKEN FOR USER AND PASS IT AT EVERY REQUEST TO VALIDATE THE CORRECT USER REQUEST              	
                		$rand  = str_random(10);
                		$api_access_token = $rand.time();
	                	$enapi_access_token = Hash::make($api_access_token);
	                	
                		$user = User::where('id','=',$arrUserDetails['id'])->update(['api_access_token' => $enapi_access_token]);

	                	$code = config('constants.SUCCESS');
	                	$status = config('constants.STATUS_SUCCESS');
	                	$msg = __('api.MSG_USER_VALIDATED');
	                    
	                	$response = array();			    		
			    		$response['user_detail'] = [
	                                'user_id' => $arrUserDetails->id,
	                                'email' => $arrUserDetails->email,
	                                'name' => $arrUserDetails->name,
	                                'mobile' => $arrUserDetails->mobile_number	,
	                                'api_access_token' => $api_access_token,
	                              ];
	                    $response_data = $response;
					} else {
						## IF OTP IS EXPIRED
						$code = config('constants.SUCCESS');			    		
			    		$msg = __('api.MSG_OTP_EXPIRED');
					}
		    		
				} else {
                    ## IF OTP IS NOT VALID 
                    $code = config('constants.SUCCESS');		            
		            $msg = __('api.MSG_INVALID_OTP');
                }			
			} else {
				## IF USER IS NOT VALID
				$code = config('constants.SUCCESS');			    
			    $msg = __('api.MSG_INVALID_USER');	
			}
		}
		## RETURN JSON RESPONSE
		return response()->json(['status'=>$status, 'code' => $code, 'message' => $msg, 'data' => $response_data]);
	}    
     
}
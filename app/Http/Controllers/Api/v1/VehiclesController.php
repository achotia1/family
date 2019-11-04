<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\VehiclesModel;

class VehiclesController extends BaseController
{
    /**
	 * @api {post} /Vehicle Info App Vehicle Info
	 * @apiDescription Handle a Vehicle Info request to the mobile application.
	 * @apiGroup Webservices
	 *
	 * @apiVersion 0.1.0
	 * @apiParam {String} user_id user_id.
	 * @apiParam {String} api_access_token api_access_token.
	 * @apiParam {String} chassis_number chassis_number. 
	 * 
	 */
    public function vehicleInfo(Request $request)
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
			  'api_access_token' => 'required',
			  'ol_number' => 'required',	  
			],
			[
			  'user_id.required'	=> __('api.MSG_USER_ID_REQ'),
			  'api_access_token.required' => __('api.MSG_ACCESS_TOKEN_REQ'),
			  'ol_number.required' => __('api.MSG_CHASSIS_REQ'),
			],
		);		
		
		## THROUGH ERROR IF VALIDATION FAILS
		if ($validator->fails()) {
            $msg = $validator->errors()->first();           
            $flag = false;
        }
    	//$validated = $request->validated();
    	//dd($validated);
        if($flag){
        	## CHECK IF USERID AND API ACCESS TOKEN ARE VALID        	
        	if($this->checkValidRequest($data)){        		
        		
        		## GET VEHICLE DETAILS
        		$query = VehiclesModel::select(['id', 'chassis_number', 'registration_number', 'ol_number', 'capacity', 'ol_status', 'id_number', 'permit_start_date', 'permit_end_date', 'type', 'status'])->where(['ol_number' => $data['ol_number'], 'status'=>config('constants.STATUS_ACTIVE')])->orderBy('id'); 	
				$vehicleDetails = $query->get()->first();
				
				if(!empty($vehicleDetails)) {
					$code = config('constants.SUCCESS');
    				$status = config('constants.STATUS_SUCCESS');    				
    				$msg = __('api.SUCCESS_MSG');
    				
    				$response = array();		    		
		    		$response['vehicle_info'] = [
                              	'id' => $vehicleDetails->id,
                              	'chassis_number' => $vehicleDetails->chassis_number,
                              	'registration_number' => $vehicleDetails->registration_number,
                              	'ol_number' => $vehicleDetails->ol_number,
                              	'capacity' => $vehicleDetails->capacity,
                              	'id_number' => $vehicleDetails->id_number,
                              	'type' => $vehicleDetails->type,
                              	'status' => $vehicleDetails->status,

                            ];                    
		    		$response_data = $response;
				} else {
					## IF RECORD NOT FOUND
					$code = config('constants.SUCCESS');
		            $msg = __('api.RECORDS_NOT_FOUND');
		            $response_data = [];
				}
        	} else {
        		## IF USER IS INVALID OR ACCESS TOKEN IS INVALID
        		$code = config('constants.SUCCESS');				
				$msg = __('api.MSG_INVALID_REQUEST');
        	}
        }  
		
		## RETURN JSON RESPONSE
    	return response()->json(['status'=>$status, 'code' => $code, 'message' => $msg, 'data' => $response_data]);
    }    
      
}
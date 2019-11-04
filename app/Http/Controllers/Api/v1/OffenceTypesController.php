<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OffencesModel;

class OffenceTypesController extends BaseController
{
    /**
	 * @api {post} /OffenceTypes App OffenceTypes
	 * @apiDescription Handle a OffenceTypes request to the mobile application.
	 * @apiGroup Webservices
	 *
	 * @apiVersion 0.1.0
	 * @apiParam {String} user_id user_id.
	 * @apiParam {String} api_access_token api_access_token.	 
	 * 
	 */
    public function index(Request $request)
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
			],
			[
			  'user_id.required'	=> __('api.MSG_USER_ID_REQ'),
			  'api_access_token.required' => __('api.MSG_ACCESS_TOKEN_REQ'),
			],
		);		
		
		## THROUGH ERROR IF VALIDATION FAILS
		if ($validator->fails()) {
            $msg = $validator->errors()->first();           
            $flag = false;
        }

        if($flag){
        	## CHECK IF USERID AND API ACCESS TOKEN ARE VALID        	
        	if($this->checkValidRequest($data)){
        		$query = OffencesModel::select(['id', 'name', 'description', 'penalty', 'status'])->where('status',config('constants.STATUS_ACTIVE'))->orderBy('id');  	
				$offenceTypes = $query->get()->toArray();
				if(!empty($offenceTypes)) {
		                $response = array();
		                foreach($offenceTypes as $key => $val) {
		                	$response['offence_types'][] = [
		                		'id' => $val['id'],
		                        'name' => $val['name'],
		                        'description' => $val['description'],
		                        'penalty' => $val['penalty'],
		                        'status' => $val['status'],
		                		];
		                }
		                $code = config('constants.SUCCESS');
		                $status = config('constants.STATUS_SUCCESS');	                
		                $msg = __('api.SUCCESS_MSG');
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
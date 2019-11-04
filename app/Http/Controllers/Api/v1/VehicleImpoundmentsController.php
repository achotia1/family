<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\VehicleImpoundmentModel;
use App\Models\ImpoundmentOffencesModel;

class VehicleImpoundmentsController extends BaseController
{
     public function index(Request $request) {
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
			  'vehicle_id' => 'required',	  
			],
			[
			  'user_id.required'	=> __('api.MSG_USER_ID_REQ'),
			  'api_access_token.required' => __('api.MSG_ACCESS_TOKEN_REQ'),
			  'vehicle_id.required' => __('api.MSG_VEHICLE_ID_REQ'),
			],
		);		
		
		## THROUGH ERROR IF VALIDATION FAILS
		if ($validator->fails()) {
            $msg = $validator->errors()->first();           
            $flag = false;
        }

        ## CHECK IF OFFSET IS SET
        $intOffset = 0;
		if(isset($data['offset']) && $data['offset'] != '') {
			$intOffset = $data['offset'];
		}

        if($flag){
        	## CHECK IF USERID AND API ACCESS TOKEN ARE VALID        	
        	if($this->checkValidRequest($data)){
        		## GET VEHICLE IMPOUNDED HISTORY
        		$query = VehicleImpoundmentModel::with([
        			'get_offences' => function($q){
						$q->with(['offence' => function($q){
							$q->select(['id', 'name','penalty']);
						}]);
					},
					'user' => function($q){
						$q->select(['id','name','email','mobile_number']);
					}
				])->where(['vehicle_id' => $data['vehicle_id'], 'status'=>config('constants.STATUS_ACTIVE')]);
        		
        		$vehicleImpoundments = $query->orderBy('id', 'DESC')->offset($intOffset)->limit(config('constants.API_PAGE_LIMIT'))->get()->toArray();        		
				
				if(!empty($vehicleImpoundments)) {
					$response = array();
            		$impoundment_data = array();
            		foreach ($vehicleImpoundments as $key => $value) {
            			$offences = $value['get_offences'];
            			$offences_arr = array();
            			$total_penalty = 0;
            			if(count($offences) > 0) {
            				foreach ($offences as $offence_key => $offence_value) {
            					## GET TOTAL PENALTY OF ALL OFFENCES AGAINST VEHICLE
            					$total_penalty += $offence_value['offence']['penalty'];
            					$offences_arr[] = [
                            		'offence_id' => $offence_value['offence_id'],
                            		'name' => $offence_value['offence']['name'],
                            		'penalty' => $offence_value['offence']['penalty'],
                        		];
            				}	
            			}
            			## PENALTY STATUS 0: UNPAID, 1: PAID
            			$penalty_status = ($value['penalty_status'] == 0) ? 'Unpaid' : 'Paid';
            			$impoundment_data[] = [
		                    'id' => $value['id'],
		                    'vehicle_id' => $value['vehicle_id'],                    
		                    'note' => $value['note'],
		                    'impounded_date' => $value['created_at'],
		                    'penalty_status' => $penalty_status,		                    
		                    'traffic_officer' => $value['user'],
		                    'total_penalty' => $total_penalty,
		                    'impoundment_offences' => $offences_arr,	                    
		                ];
		                $response['impounded_list'] = $impoundment_data;
            		}
            		$code = config('constants.SUCCESS');
		            $status = config('constants.STATUS_SUCCESS');	                
		            $msg = __('api.SUCCESS_MSG');
            		$response_data = $response;
				}
				//dd($record);
        	} else {
        		## IF USER IS INVALID OR ACCESS TOKEN IS INVALID
        		$code = config('constants.SUCCESS');				
				$msg = __('api.MSG_INVALID_REQUEST');
        	}        	
        }  
		
		## RETURN JSON RESPONSE
    	return response()->json(['status'=>$status, 'code' => $code, 'message' => $msg, 'data' => $response_data]);
    }

    public function updateVehicleStatus(Request $request) {
    	$flag  = true;
    	$code = config('constants.UNSUCCESS');
    	$status = config('constants.STATUS_UNSUCCESS');
        $msg = '';
        $response_data = (object)[];
    	$data = $request->all();

    	$firstKey = array_key_first($data['offences']);

    	## FIELD VALIDATIONS
		$validator = Validator::make($data, 
			[
			  'user_id'	=> 'required',
			  'api_access_token' => 'required',
			  'vehicle_id' => 'required',
			  'offences.'.$firstKey  => 'required',
			],
			[
			  'user_id.required'	=> __('api.MSG_USER_ID_REQ'),
			  'api_access_token.required' => __('api.MSG_ACCESS_TOKEN_REQ'),
			  'vehicle_id.required' => __('api.MSG_VEHICLE_ID_REQ'),
			  'offences.'.$firstKey.'.required' => __('api.MSG_OFFENCE_ARR_REQ'),			  
			],
		);		
		
		## THROUGH ERROR IF VALIDATION FAILS
		if ($validator->fails()) {
            $msg = $validator->errors()->first();           
            $flag = false;
        }        

        if($flag){
        	$note = (isset($data['note']) && $data['note'] != '') ? $data['note'] : '';
        	
        	$modelVehicleImpoundment = new VehicleImpoundmentModel();
            $modelVehicleImpoundment->vehicle_id = trim($data['vehicle_id']);
            $modelVehicleImpoundment->user_id = trim($data['user_id']);
            $modelVehicleImpoundment->note = trim($note);
            
            if($modelVehicleImpoundment->save()) {
               $impoundment_id = $modelVehicleImpoundment->id;
               if(!empty($data['offences'])) {
	            	foreach($data['offences'] as $offence_id){
	            		$offenceModel = new ImpoundmentOffencesModel();
	            		$offenceModel->impoundment_id = $impoundment_id;
	            		$offenceModel->offence_id = $offence_id;
	            		$offenceModel->save();	            		
	            	}
            	}
               $code = config('constants.SUCCESS');
		       $status = config('constants.STATUS_SUCCESS');
		       $msg = __('api.MSG_VEHICLE_STATUS_UPDATED');
            }
        }  
		
		## RETURN JSON RESPONSE
    	return response()->json(['status'=>$status, 'code' => $code, 'message' => $msg, 'data' => $response_data]);
    }
}

<?php
namespace App\Traits;
use App\Models\CompanyModel;

use DB;
use Request;
use Browser;

trait GeneralTrait
{

    public function __construct(CompanyModel $CompanyModel) 
    {
        $this->CompanyModel = $CompanyModel;
    }
    public function _getCompanyId()
    {
        return session('company_id');
    }

    public function _setCompanyId($company_id=false)
    {
        if(empty($company_id)){
            session(['company_id' => '']);
        }elseif(empty(session('company_id')) && isset($company_id)){
            session(['company_id' => $company_id]);
        }

        return session('company_id');
    }

    public function _getCompanyDetails($company_id=false)
    {   
        if(empty($company_id)){
            $company_id = $this->_getCompanyId();
        }

        $company = $this->CompanyModel->find($company_id);

        return $company;
    }

    public function _checkCompanyUniqueUser($user_id=false,$email=false)
    {
        // dd('_checkCompanyUser');
        $companyId = self::_getCompanyId();

        $where = "";
        if(!empty($user_id)){
           $where = " AND users.id!=".$user_id; 
        }

        if(!empty($email)){
            $sqlQuery = "select count(1) as cnt,".$companyId." as currentCompany,users.company_id from users
                        where status=1 and email='".$email."'".$where." 
                        GROUP by users.id";
            // and company_id='".$companyId."'
            $collections = collect(DB::select($sqlQuery));
        }   

        // return $count->cnt;            
        return $collections->first();
    }

    public function _sendSms($phones,$text){

        if(!empty($phones) && !empty($text)){
                                     
            $message=urlencode($text);
            //If more than one phone number then it must be separated by comma
            // $phones="9810012345,9977437333"; 
            $url        = config('constants.SMS_URL'); 
            $username   = config('constants.SMS_USERNAME');
            $password   = config('constants.SMS_PASSWORD');
            $sender_id  = config('constants.SMS_SENDERID');
            

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "username=".$username."&password=".$password."&sender=".$sender_id."&numbers=".$phones."&message=".$message);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

            //get response
            $response = curl_exec($ch);
           
            //Print error if any
            $isError = false;
            if (curl_errno($ch)) {
                $isError = true;
                $errorMessage = curl_error($ch);
            }
            curl_close($ch);

            if($isError){
                return array('error' => 1 , 'message' => $errorMessage);
            }else{
                return array('error' => 0 , 'message' => 'Success');
            }
        }else{
            $errorMessage = "Mobile Number and Message fields are required";
            return array('error' => 1 , 'message' => $errorMessage);
        }
        
    }

    public function _getBatchMaterials($batch_id,$material_id=false,$module=false){

        $company_id  = self::_getCompanyId();

        $get_material_id = $this->BaseModel
                             ->where('batch_no',$batch_id)
                             ->get(['material_id']);
        $material_ids = array_column($get_material_id->toArray(), "material_id");
        
        if(!empty($material_id)){
            $material_ids= array_diff($material_ids, [$material_id]);
        }
        $raw_materials = $this->StoreRawMaterialModel
                              ->whereNotIn("id",$material_ids)
                              ->get(['id','name']);

        //Show only Production Materials for Issues and Returned Materials
        if(!empty($module) && $module=="non_material_module"){
            $get_production_batches = $this->StoreProductionModel
                                     ->where('batch_no',$batch_id)
                                     ->get(['material_id']);
            $production_material_ids = array_column($get_production_batches->toArray(), "material_id");
        }                              

        // dd($raw_materials->toArray(),$material_ids,$production_material_ids);

        $html="<option value=''>Select Material</option>";
        foreach($raw_materials as $material){
            $selected="";
            if($material_id==$material->id){
                $selected="selected";
            } 
            
            if(!empty($module) && $module=="non_material_module"){
                if(in_array($material->id, $production_material_ids)){
                    $html.="<option value='".$material->id."' $selected>".$material->name."</option>";
                }
            }else{
                $html.="<option value='".$material->id."' $selected>".$material->name."</option>";

            }
        }

        return $html;

    }

}
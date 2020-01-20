<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreTestModel;

use App\Traits\GeneralTrait;

use DB;
use Carbon\Carbon;
class StoreTestController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreTestModel $StoreTestModel
    )
    {
        $this->BaseModel  = $StoreTestModel;        

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Test Material';
        $this->ModuleView  = 'admin.materials.';
        $this->ModulePath = 'admin.materials.';

        ## PERMISSION MIDDELWARE
        //$this->middleware(['permission:store-material-listing'], ['only' => ['getRecords']]);
        //$this->middleware(['permission:store-material-add'], ['only' => ['edit','update','create','store','bulkDelete']]);
    }
    

    public function index()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;        
		
		for($i=0; $i<5; $i++){
			$finalArray[$i]['name'] = 'Ashvini'.$i;
        	$finalArray[$i]['email'] = 'ashvini'.$i.'@gmail.com';
        	//$finalArray[$i]['created_at'] = Carbon::now()->toDateTimeString();
        	//$finalArray[$i]['updated_at'] = Carbon::now()->toDateTimeString();
        	
		}
		//dd($finalArray);
        if(!empty($finalArray)){
        	$prodRawMaterialObj1 = new StoreTestModel;
            $prodRawMaterialObj1->insert($finalArray);
		}
        dd('test');
        //return view($this->ModuleView.'index', $this->ViewData);
    }

}

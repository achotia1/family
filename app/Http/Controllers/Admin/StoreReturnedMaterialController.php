<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreReturnedMaterialModel;
use App\Models\StoreRawMaterialModel;
use App\Models\StoreBatchCardModel;
use App\Models\StoreProductionModel;
use App\Models\ProductionHasMaterialModel;
use App\Models\StoreReturnedHasMaterialModel;

use App\Models\StoreInMaterialModel;

use App\Http\Requests\Admin\StoreReturnedMaterialRequest;
use App\Traits\GeneralTrait;
use DB;

class StoreReturnedMaterialController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreReturnedMaterialModel $StoreReturnedMaterialModel,
        StoreRawMaterialModel $StoreRawMaterialModel,
        StoreProductionModel $StoreProductionModel,
        ProductionHasMaterialModel $ProductionHasMaterialModel,
        StoreInMaterialModel $StoreInMaterialModel,
        StoreReturnedHasMaterialModel $StoreReturnedHasMaterialModel
    )
    {
        $this->BaseModel  = $StoreReturnedMaterialModel;
        $this->StoreRawMaterialModel  = $StoreRawMaterialModel;
        $this->StoreProductionModel  = $StoreProductionModel;
        $this->ProductionHasMaterialModel  = $ProductionHasMaterialModel;
        $this->StoreInMaterialModel  = $StoreInMaterialModel;
        $this->StoreReturnedHasMaterialModel  = $StoreReturnedHasMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Returned Material';
        $this->ModuleView  = 'admin.store-returned-material.';
        $this->ModulePath = 'admin.return.';

        ## PERMISSION MIDDELWARE
        /*$this->middleware(['permission:manage-materials'], ['only' => ['edit','update','create','store','getRecords','bulkDelete']]);*/
    }
    

    public function index()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;        

        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'index', $this->ViewData);
    }

    public function create()
    {                
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Add New '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['moduleAction'] = 'Add New '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;
        
        $objStore = new StoreBatchCardModel;
        $batchNos = $objStore->getBatchNumbers();

         // dd($batchNos);

        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getMaterialNumbers();
        $this->ViewData['materialIds']   = $materialIds;
        $this->ViewData['batchNos']   = $batchNos;
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreReturnedMaterialRequest $request)
    {        
       // dd($request->all());
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create record, Something went wrong on server.'; 

        try {     

            $companyId = self::_getCompanyId();      

            $collection = new $this->BaseModel;   
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection)
            {
                $all_transactions = [];
                ## ADD PRODUCTION RAW MATERIAL DATA
                if (!empty($request->returned) && sizeof($request->returned) > 0) 
                {                    
                    ## ADD IN store_has_production_materials
                    foreach ($request->returned as $pkey => $return) 
                    {
                        $returnRawMaterialObj = new $this->StoreReturnedHasMaterialModel;
                        $returnRawMaterialObj->returned_id   = $collection->id;
                        $returnRawMaterialObj->material_id   = !empty($return['material_id']) ? $return['material_id'] : 0;
                        $returnRawMaterialObj->lot_id   =  !empty($return['lot_id']) ? $return['lot_id'] : 0;
                        $returnRawMaterialObj->quantity   = !empty($return['quantity']) ? $return['quantity'] : 0;
                        if ($returnRawMaterialObj->save()) 
                        {                            
                            ## UPDATE Production Planned QUANTITY                            
                            if($return['lot_id'] > 0)
                            {

                                $sqlQuery = "SELECT store_production_has_materials.id as spmid,store_production_has_materials.quantity FROM store_production_has_materials
                                    join store_productions ON store_production_has_materials.production_id=store_productions.id 
                                            WHERE store_productions.batch_id = '".$request->batch_id."'
                                            AND store_productions.company_id = '".$companyId."'
                                            AND store_production_has_materials.material_id = '".$return['material_id']."'
                                            AND store_production_has_materials.lot_id = '".$return['lot_id']."'
                                            ";
                                $collectionReturn = collect(DB::select(DB::raw($sqlQuery)));

                                if(!empty($collectionReturn) && count($collectionReturn)>0)
                                {

                                    $returnData = $collectionReturn->first();
                                    if($returnData->spmid){
                                        //$updateQty = $returnData->quantity+$return['quantity'];
                                        ##update returned quantity in production and update returned qty+actual qty in store
                                        $updateQtyQry = DB::table('store_production_has_materials')
                                                            ->where('id', $returnData->spmid)
                                                            ->update(['returned_quantity' => $return['quantity']]);

                                        $updateLotBalance = $this->StoreInMaterialModel->find($return['lot_id']);
                                        if($updateLotBalance){
                                            $updateLotBalance->lot_balance=$updateLotBalance->lot_balance+$return['quantity'];
                                            if ($updateLotBalance->save()) 
                                            {
                                               $all_transactions[] = 1;
                                            }else{
                                               $all_transactions[] = 0;
                                            }

                                        }


                                    }

                                }
                            }
                            
                            $all_transactions[] = 1;
                           
                        }
                        else
                        {
                            $all_transactions[] = 0;
                        }
                        
                    }
               
                }
            }

            if (!in_array(0,$all_transactions)) 
            {
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route($this->ModulePath.'index');
                $this->JsonData['msg'] = $this->ModuleTitle.' added successfully.';
                DB::commit();
            }else
            {
                DB::rollback();
                $this->JsonData['error_msg'] = $e->getMessage();
            }

        }
        catch(\Exception $e) {
            DB::rollback();
            $this->JsonData['error_msg'] = $e->getMessage();
            $this->JsonData['msg'] = __('admin.ERR_SOMETHING_WRONG');
        }

        return response()->json($this->JsonData);
    }

   /* public function show($encID)
    {
       // Default site settings
        $this->ViewData['moduleTitle']  = 'View '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'View '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        // All data
        $this->ViewData['vehicle'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));

        // view file with data
        return view($this->ModuleView.'view', $this->ViewData);
    }*/

    public function edit($encID)
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['modulePath']   = $this->ModulePath;
		
        $companyId = self::_getCompanyId();

        $data = $this->BaseModel->with([   
                            'hasReturnedMaterials' => function($q)
                            {  
                                $q->with('material');
                                $q->with(['lot' => function($q1)
                                        {  
                                            $q1->with('hasProductionMaterial');
                                        }]
                                    );
                            }
                        ])
                    ->where('store_returned_materials.id', base64_decode(base64_decode($encID)))
                    ->where('store_returned_materials.company_id', $companyId)
                    ->first();
        // dd($data->toArray());
        if(empty($data)) {            
            return redirect()->route('admin.return.index');
        }

		$objStore = new StoreBatchCardModel;
        $batchNos = $objStore->getBatchNumbers();

        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getMaterialNumbers();       
        
        $this->ViewData['batchNos']   = $batchNos;
        $this->ViewData['materialIds']   = $materialIds;
        
        ## ALL DATA
        /*$this->ViewData['return_material'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));*/
        $this->ViewData['return_material'] = $data;        
        $this->ViewData['freturn_date'] = date('d-m-Y',strtotime($this->ViewData['return_material']->return_date));
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreReturnedMaterialRequest $request, $encID)
    {
       // dump($request->all());
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Return Material, Something went wrong on server.';       

        $id = base64_decode(base64_decode($encID));
        try {     

            $companyId = self::_getCompanyId();      

            $collection = $this->BaseModel->find($id);   
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection)
            {
                $all_transactions = [];
                ## ADD PRODUCTION RAW MATERIAL DATA
                if (!empty($request->returned) && sizeof($request->returned) > 0) 
                {   

                    $previousReturnedMaterials=$this->StoreReturnedHasMaterialModel
                                                    ->where('returned_id', $collection->id)
                                                    ->get();

                    //Update lot balance for all the previous materials
                    if(!empty($previousReturnedMaterials))
                    {
                        foreach($previousReturnedMaterials as $previous) 
                        {
                            $sqlQuery = "SELECT store_production_has_materials.id as spmid,store_production_has_materials.quantity,store_production_has_materials.returned_quantity FROM store_production_has_materials
                                    join store_productions ON store_production_has_materials.production_id=store_productions.id 
                                            WHERE store_productions.batch_id = '".$request->batch_id."'
                                            AND store_productions.company_id = '".$companyId."'
                                            AND store_production_has_materials.material_id = '".$previous->material_id."'
                                            AND store_production_has_materials.lot_id = '".$previous->lot_id."'";
                            $collectionReturn = collect(DB::select(DB::raw($sqlQuery)));

                            ## UPDATE Production Planned QUANTITY                            
                            if(!empty($collectionReturn) && count($collectionReturn)>0)
                            {
                                $returnData = $collectionReturn->first();
                               // dump($collectionReturn);
                                if($returnData->spmid)
                                {
                                   ##update returned quantity in production and update returned qty+actual qty in store
                                    $updateLotBalance = $this->StoreInMaterialModel->find($previous->lot_id);
                                    if($updateLotBalance)
                                    {
                                        $updateLotBalance->lot_balance=($updateLotBalance->lot_balance-$returnData->returned_quantity);
                                        if($updateLotBalance->save()) 
                                        {
                                           $all_transactions[] = 1;
                                        }else{
                                           $all_transactions[] = 0;
                                        }

                                    }

                                }

                            }
                        }

                    }

                    //dd($request->returned);

                    //Delete records
                    $this->StoreReturnedHasMaterialModel->where('returned_id', $collection->id)->delete();

                    //Update return quantity and lot balance for the current items
                    ## ADD IN store_has_production_materials
                    foreach ($request->returned as $pkey => $return) 
                    {
                        $returnRawMaterialObj = new $this->StoreReturnedHasMaterialModel;
                        $returnRawMaterialObj->returned_id   = $collection->id;
                        $returnRawMaterialObj->material_id   = !empty($return['material_id']) ? $return['material_id'] : 0;
                        $returnRawMaterialObj->lot_id   =  !empty($return['lot_id']) ? $return['lot_id'] : 0;
                        $returnRawMaterialObj->quantity   = !empty($return['quantity']) ? $return['quantity'] : 0;
                        if ($returnRawMaterialObj->save()) 
                        {                            
                            ## UPDATE Production Planned QUANTITY                            
                            if($return['lot_id'] > 0)
                            {

                                $sqlQuery = "SELECT store_production_has_materials.id as spmid,store_production_has_materials.quantity FROM store_production_has_materials
                                    join store_productions ON store_production_has_materials.production_id=store_productions.id 
                                            WHERE store_productions.batch_id = '".$request->batch_id."'
                                            AND store_productions.company_id = '".$companyId."'
                                            AND store_production_has_materials.material_id = '".$return['material_id']."'
                                            AND store_production_has_materials.lot_id = '".$return['lot_id']."'
                                            ";
                                $collectionReturn = collect(DB::select(DB::raw($sqlQuery)));

                                if(!empty($collectionReturn) && count($collectionReturn)>0)
                                {

                                    $returnData = $collectionReturn->first();

                                    if($returnData->spmid){
                                        ##update returned quantity in production and update returned qty+actual qty in store
                                        $updateQtyQry = DB::table('store_production_has_materials')
                                                            ->where('id', $returnData->spmid)
                                                            ->update(['returned_quantity' => $return['quantity']]);

                                        $updateLotBalance = $this->StoreInMaterialModel->find($return['lot_id']);
                                        if($updateLotBalance){
                                            $updateLotBalance->lot_balance=$updateLotBalance->lot_balance+$return['quantity'];
                                            if ($updateLotBalance->save()) 
                                            {
                                               $all_transactions[] = 1;
                                            }else{
                                               $all_transactions[] = 0;
                                            }

                                        }


                                    }

                                }
                            }
                            
                            $all_transactions[] = 1;
                           
                        }
                        else
                        {
                            $all_transactions[] = 0;
                        }
                        
                    }

                }//ifclose
               
            }

            if (!in_array(0,$all_transactions)) 
            {
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route($this->ModulePath.'index');
                $this->JsonData['msg'] = $this->ModuleTitle.' updated successfully.';
                DB::commit();
            }else
            {
                DB::rollback();
                $this->JsonData['error_msg'] = $e->getMessage();
            }

        }
        catch(\Exception $e) {
            DB::rollback();
            $this->JsonData['error_msg'] = $e->getMessage();
            $this->JsonData['msg'] = __('admin.ERR_SOMETHING_WRONG');
        }

        return response()->json($this->JsonData);

    }

    public function destroy($id)
    {
        //
    }

    public function _storeOrUpdate($collection, $request)
    {
        
        $collection->company_id  = self::_getCompanyId();
        $collection->batch_id    = $request->batch_id;
        $collection->return_date = date('Y-m-d',strtotime($request->return_date));   
        //Save data
       $collection->save();
        
        return $collection;
    }

    public function getRecords(Request $request)
    {

        /*--------------------------------------
        |  VARIABLES
        ------------------------------*/

        ## SKIP AND LIMIT
        $start = $request->start;
        $length = $request->length;

        ## SEARCH VALUE
        $search = $request->search['value']; 

        ## ORDER
        $column = $request->order[0]['column'];
        $dir = $request->order[0]['dir'];

        ## FILTER COLUMNS
        $filter = array(
            0 => 'store_returned_materials.id',
            1 => 'store_returned_materials.id',
            2 => 'store_returned_materials.batch_id',
            3 => 'store_returned_materials.return_date',
            4 => 'products.code',
            5 => 'store_returned_materials.id',
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/
        $companyId = self::_getCompanyId();
        ## START MODEL QUERY    
        //$modelQuery =  $this->BaseModel;
       $modelQuery =  $this->BaseModel        
        // ->leftjoin('store_returned_materials_has_materials', 'store_returned_materials.id' , '=', 'store_returned_materials_has_materials.returned_id')       
        // ->leftjoin('store_raw_materials', 'store_raw_materials.id' , '=', 'store_returned_materials_has_materials.material_id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_returned_materials.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
        ->where('store_returned_materials.company_id', $companyId);
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {   
            if (!empty($request->custom['batch_id'])) 
            {
                $custom_search = true;

                $key = $request->custom['batch_id'];
                $modelQuery = $modelQuery
                    ->where('batch_id', $key);

            }

           
            if (!empty($request->custom['product_name'])) 
            {
                $custom_search = true;

                $key = $request->custom['product_name'];
                $modelQuery = $modelQuery
                ->where('products.code',  'LIKE', '%'.$key.'%');

            }
                
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_returned_materials.id', 'DESC');           
                //$modelQuery = $modelQuery->orderBy('vehicles.chassis_number', 'ASC');           
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get(['store_returned_materials.id', 
            'store_batch_cards.batch_card_no',
            'store_returned_materials.return_date',
           // 'store_returned_materials.material_id',
            //'store_returned_materials.quantity',
           // 'store_raw_materials.name',
            'products.name as prod_name',
            'products.code as prod_code',            
        ]);    

         // dd($object->toArray());


        /*--------------------------------------
        |  DATA BINDING
        ------------------------------*/

        $data = [];

        if (!empty($object) && sizeof($object) > 0)
        {
            $count =1;
            foreach ($object as $key => $row)
            {

                $data[$key]['id'] = $row->id;

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="sales[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';
                $data[$key]['batch_id']  = $row->batch_card_no;

                $data[$key]['return_date'] = date('d M Y',strtotime($row->return_date));
                $data[$key]['product_name']  =  $row->prod_code." ( ".$row->prod_name." )";
                /*if($row->status==1){
                    $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                }elseif($row->status==0) {
                 $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Inactive</span>';
                }    */            
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';
                //$data[$key]['actions'] = '';               
                $data[$key]['actions'] =  '<div class="text-center">'.$edit.'</div>';
               

         }
     }

    $objStore = new StoreBatchCardModel;
    $batchNos = $objStore->getBatchNumbers();

    $batch_no_string = '<select name="batch_no" id="batch-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_id'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';    

    
    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    $searchHTML['batch_id'] = $batch_no_string;
    $searchHTML['return_date']     =  '';    
    $searchHTML['product_name'] = '<input type="text" class="form-control" id="product-name" value="'.($request->custom['product_name']).'" placeholder="Search...">';
    //$searchHTML['status']   =  '';

    $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';

    $searchHTML['actions'] = $seachAction;


    array_unshift($data, $searchHTML);

    ## WRAPPING UP
    $this->JsonData['draw']             = intval($request->draw);
    $this->JsonData['recordsTotal']     = intval($totalData);
    $this->JsonData['recordsFiltered']  = intval($totalFiltered);
    $this->JsonData['data']             = $data;

    return response()->json($this->JsonData);
}

    public function bulkDelete(Request $request)
    {
        $companyId = self::_getCompanyId();

        DB::beginTransaction();
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to delete materials, Something went wrong on server.';

        if (!empty($request->arrEncId)) 
        {
            $arrID = array_map(function($item)
            {
                return base64_decode(base64_decode($item));

            }, $request->arrEncId);

            try 
            {
                $object = $this->BaseModel->whereIn('id', $arrID)->get();
                foreach ($object as $key => $collection) 
                {

                    if(!empty($collection->id))
                    {

                        $returnedMaterialObject = $this->StoreReturnedHasMaterialModel->where('returned_id', $collection->id)->get();
                    if(!empty($returnedMaterialObject) && count($returnedMaterialObject)>0)
                    {
                        foreach($returnedMaterialObject as $mkey => $mvalue) 
                        {
                           

                             $sqlQuery = "SELECT store_production_has_materials.id as spmid,store_production_has_materials.quantity,store_production_has_materials.returned_quantity FROM store_production_has_materials
                                        join store_productions ON store_production_has_materials.production_id=store_productions.id 
                                                WHERE store_productions.batch_id = '".$collection->batch_id."'
                                                AND store_productions.company_id = '".$companyId."'
                                                AND store_production_has_materials.material_id = '".$mvalue->material_id."'
                                                AND store_production_has_materials.lot_id = '".$mvalue->lot_id."'";
                            $collectionReturn = collect(DB::select(DB::raw($sqlQuery)));
                            if(!empty($collectionReturn) && count($collectionReturn)>0)
                            {
                                $returnData = $collectionReturn->first();
                                if($returnData->spmid)
                                {
                                   ##update returned quantity in production and update returned qty+actual qty in store
                                    $updateQtyQry = DB::table('store_production_has_materials')
                                                                ->where('id', $returnData->spmid)
                                                                ->update(['returned_quantity' => 0]);

                                    $updateLotBalance = $this->StoreInMaterialModel->find($mvalue->lot_id);
                                    if($updateLotBalance)
                                    {
                                        $updateLotBalance->lot_balance=($updateLotBalance->lot_balance-$returnData->returned_quantity);
                                        if($updateLotBalance->save()) 
                                        {
                                           $all_transactions[] = 1;
                                        }else{
                                           $all_transactions[] = 0;
                                        }

                                    }

                                }

                            }
                        }
                    }

                    
                    $this->StoreReturnedHasMaterialModel->where('returned_id',$collection->id)->delete();

                    }
                    
                    $this->BaseModel->where('id', $collection->id)->delete();
                }

                DB::commit();

                $this->JsonData['status'] = 'success';
                $this->JsonData['msg'] = $this->ModuleTitle.' deleted successfully.';

            } catch (Exception $e) 
            {
               $this->JsonData['exception'] = $e->getMessage();
                DB::rollback();
           }
       }

       return response()->json($this->JsonData);   
    }

    public function getBatchMaterials(Request $request)
    {
        // dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get batch materials, Something went wrong on server.';
        try 
        {
            // $material_id   = $request->material_id;
            $batch_id   = $request->batch_id;
            $companyId = self::_getCompanyId();
           // dd($companyId);
            // echo "string:".$batch_id;

            $get_production_batches = $this->StoreProductionModel
                                        ->join('store_production_has_materials','production_id','store_productions.id')
                                        ->where('batch_id',$batch_id)
                                        ->where('company_id',$companyId)
                                        ->get(['material_id']);
                                       // ->with(['hasProductionMaterials'])
           // dd($get_production_batches->toArray());
            $material_ids = array_column($get_production_batches->toArray(), "material_id");

            $raw_materials = $this->StoreRawMaterialModel
                                  ->whereIn("id",$material_ids)
                                  ->get(['id','name']);
           /* $raw_materials = $this->StoreRawMaterialModel
                                    ->join('store_productions','store_productions.batch_id','store_productions.id')
                                    ->join('store_production_has_materials','production_id','store_productions.id')
                                    ->where('batch_id',$batch_id)
                                  // ->whereIn("id",$material_ids)
                                      ->get(['id','name']);*/
            // dd($raw_materials);
            $html="<option value=''>Select Material</option>";
            foreach($raw_materials as $material){
                $selected="";
                /*if($material_id==$material->id){
                    $selected="selected";
                } */
                
                $html.="<option value='".$material->id."' $selected>".$material->name."</option>";

            }
            // dd($get_production_batches,$raw_materials);
            /*$module = "non_material_module";
            if(!empty($material_id)){
                $html       = self::_getBatchMaterials($batch_id,$material_id,$module);
            }else{
                $html       = self::_getBatchMaterials($batch_id,false,$module);
            }*/
 
            $this->JsonData['html'] = $html;
            //$this->JsonData['data'] = $raw_materials;
            $this->JsonData['msg']  = 'Raw Materials';
            $this->JsonData['status']  = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }

    public function getMaterialLots(Request $request)
    {
        // dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get material Lots, Something went wrong on server.';
        try 
        {
            $batch_id       = $request->batch_id;
            $material_id    = $request->material_id;
            $selected_val   = $request->selected_val;
            $companyId = self::_getCompanyId();
           // dd($companyId);
            $get_lot_ids = $this->StoreProductionModel
                                        ->join('store_production_has_materials','production_id','store_productions.id')
                                        ->where('batch_id',$batch_id)
                                        ->where('material_id',$material_id)
                                        ->where('company_id',$companyId)
                                        ->get(['lot_id']);
            // dd($get_lot_ids->toArray());      
            $lot_ids = array_column($get_lot_ids->toArray(), "lot_id");
                                  
            $get_material_lots = $this->StoreInMaterialModel
                                        ->join('store_production_has_materials','lot_id','store_in_materials.id')
                                      ->whereIn("store_in_materials.id",$lot_ids)
                                      ->get(['store_in_materials.id','store_in_materials.material_id','lot_no','lot_qty','lot_balance','store_production_has_materials.quantity as production_quantity']);
            //dd($get_material_lots);

            $html="<option value=''>Select Lot</option>";
            foreach($get_material_lots as $lotId){        
                $selected="";            
                if (!in_array($lotId['id'], $selected_val))
                {
                    $html.="<option data-qty='".$lotId['production_quantity']."' value='".$lotId['id']."' $selected>".$lotId['lot_no']." (".$lotId['production_quantity'].")</option>";
                }                        
            }
            // $material_id   = $request->material_id;
            // $selected_val      = $request->selected_val;      
            // if(!empty($material_id)){
            //     $html       = self::_getMaterialLots($material_id, $selected_val);
            // }
 
            $this->JsonData['html'] = $html;
            //$this->JsonData['data'] = $raw_materials;
            $this->JsonData['msg']  = 'Material Lots';
            $this->JsonData['status']  = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }

}
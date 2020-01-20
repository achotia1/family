<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


## MODELS
use App\Models\StoreProductionModel;
use App\Models\StoreBatchCardModel;
use App\Models\StoreRawMaterialModel;
use App\Models\StoreInMaterialModel;
use App\Models\ProductionHasMaterialModel;
use App\Models\ProductsModel;
use App\Models\StoreOutMaterialModel;
use App\Models\StoreReturnedMaterialModel;
use App\Models\StoreWasteStockModel;
use App\Models\StoreReuseWastageModel;
use App\Models\StoreReuseWastageHasMaterialsModel;
use App\Models\StoreMaterialOpeningModel;

use App\Http\Requests\Admin\StoreProductionRequest;
use App\Traits\GeneralTrait;

use DB;
use Carbon\Carbon;
class StoreProductionController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreProductionModel $StoreProductionModel,
        StoreRawMaterialModel $StoreRawMaterialModel,
        StoreOutMaterialModel $StoreOutMaterialModel,
        StoreReturnedMaterialModel $StoreReturnedMaterialModel,
        StoreBatchCardModel $StoreBatchCardModel,
        StoreWasteStockModel $StoreWasteStockModel,
        StoreReuseWastageModel $StoreReuseWastageModel,
        StoreReuseWastageHasMaterialsModel $StoreReuseWastageHasMaterialsModel
    )
    {
        $this->BaseModel  = $StoreProductionModel;
        $this->StoreProductionModel  = $StoreProductionModel;
        $this->StoreRawMaterialModel  = $StoreRawMaterialModel;
        $this->StoreOutMaterialModel  = $StoreOutMaterialModel;
        $this->StoreReturnedMaterialModel  = $StoreReturnedMaterialModel;
        $this->StoreBatchCardModel = $StoreBatchCardModel;
        $this->StoreWasteStockModel = $StoreWasteStockModel;
        $this->StoreReuseWastageModel = $StoreReuseWastageModel;
        $this->StoreReuseWastageHasMaterialsModel = $StoreReuseWastageHasMaterialsModel;


        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Production';
        $this->ModuleView  = 'admin.store-production.';
        $this->ModulePath = 'admin.production.';

        $this->wastageMaterialRecords = array(
                                                0=>'Course',
                                                1=>'Rejection',
                                                2=>'Dust',
                                                3=>'Loose'
                                            );

        ## PERMISSION MIDDELWARE
        $this->middleware(['permission:store-material-plan-listing'], ['only' => ['getRecords']]);
        $this->middleware(['permission:store-material-plan-add'], ['only' => ['edit','update','create','store','destroy']]);
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


        $companyId = self::_getCompanyId();
        
        /*$rcester_companyId = config('constants.RCESTERCOMPANY');
        $showWastage = true;
        if($companyId==$rcester_companyId){
            $showWastage = false;
        }*/
        // $objStore = new StoreBatchCardModel();
        //$batchNos  = $objStore->getBatchNumbers($companyId,true);
        $batchNos  = $this->StoreBatchCardModel->getBatchNumbers($companyId,true);
        // dd($companyId,$batchNos);

        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getLotMaterials($companyId);

        $this->ViewData['batchNos']   = $batchNos;
        $this->ViewData['materialIds']   = $materialIds;
       // $this->ViewData['showWastage'] = $showWastage;
        
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreProductionRequest $request)
    {    
        //dd($request->all());
        ##Validation for Wastage Material Stock quantity
        if (!empty($request->wastage) && count($request->wastage) > 0){
            foreach ($request->wastage as $wastage){
                if( !empty($wastage['batch_id']) && !empty($wastage['material_id']) && !empty($wastage['quantity']) ){

                    if($wastage['quantity']<=0){

                        $this->JsonData['status'] = __('admin.RESP_ERROR');
                        $this->JsonData['msg'] = 'You cannot add quantity less than one'; 
                        return response()->json($this->JsonData);
                        exit();
                    }
                    if($wastage['quantity']>$wastage['wastageQuantityLimit']){

                        $this->JsonData['status'] = __('admin.RESP_ERROR');
                        $this->JsonData['msg'] = 'You can not select more than available quantity:'.$wastage['wastageQuantityLimit']; 
                        return response()->json($this->JsonData);
                        exit();
                    }

                }
                
            }
        }

        DB::beginTransaction();
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Record, Something went wrong on server.';
        try {

            $collection = new $this->BaseModel;
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection->save()){
                $all_transactions = [];
                ## ADD PRODUCTION RAW MATERIAL DATA
                if (!empty($request->production) && sizeof($request->production) > 0) 
                {                    
                    
                    $result = array();
                    ## IF Duplicate row for same material and lot id
                    # MAKE addition of quantities and create one record
                    foreach($request->production as $val){
                        if(isset($result[$val['material_id']][$val['lot_id']])){
                            $result[$val['material_id']][$val['lot_id']] = $result[$val['material_id']][$val['lot_id']] + $val['quantity'];
                        }
                        else {
                            $result[$val['material_id']][$val['lot_id']] = $val['quantity'];
                        }
                        
                    }

                    $finalArray = $correntRecords = array();
                    $i = 0;
                    foreach($result as $materialId=>$rVal){                        
                        foreach($rVal as $lotId=>$quantity){ 
                            if($quantity<=0){

                                $this->JsonData['status'] = __('admin.RESP_ERROR');
                                $this->JsonData['msg'] = 'You cannot add quantity less than one'; 
                                DB::rollback();
                                return response()->json($this->JsonData);
                                exit();
                            }                           
                            if($quantity > 0 && $materialId > 0 && $lotId > 0){
                                $finalArray[$i]['production_id'] = $collection->id;
                                $finalArray[$i]['material_id'] = $materialId;
                                $finalArray[$i]['lot_id'] = $lotId;
                                $finalArray[$i]['quantity'] = $quantity;
                                $finalArray[$i]['created_at'] = Carbon::now()->toDateTimeString();
                                $finalArray[$i]['updated_at'] = Carbon::now()->toDateTimeString();
                                $i++;
                                $correntRecords[$lotId] = $quantity;
                            }                            
                        }   
                    }                   
                    if(!empty($finalArray)){
                        $prodRawMaterialObj1 = new ProductionHasMaterialModel;
                        $prodRawMaterialObj1->insert($finalArray);
                        ## REMOVE THE NEW PLANNED QUANTITY FROM STOCK
                        foreach($correntRecords as $cLotId=>$cQuantity){
                            $inObj = new StoreInMaterialModel;
                            $inMaterialcollection = $inObj->find($cLotId);
                            $updateBal = $inObj->updateBalance($inMaterialcollection, $cQuantity);
                            if($updateBal) 
                            {                            
                                $all_transactions[] = 1;
                            } else {
                                $all_transactions[] = 0;
                            }
                        }
                    } else {
                        $all_transactions[] = 0;
                    }
                    ## MARK BATCH AS PLAN ADDED BATCH                    
                    $batchId = $collection->batch_id;
                    $objBatch = new StoreBatchCardModel();
                    $objBatch->updatePlanAdded($batchId);
                }


                if (!empty($request->wastage) && sizeof($request->wastage) > 0) 
                {
                    /*$rcester_companyId = config('constants.RCESTERCOMPANY');
                    $companyId = self::_getCompanyId();
                    if($companyId==$rcester_companyId){
                        $this->wastageMaterialRecords[0] = 'Unfiltered';
                    }*/

                    if( !empty($request->wastage[0]['batch_id']) && !empty($request->wastage[0]['material_id']) && !empty($request->wastage[0]['quantity']) ){

                        $collectionReuse = new $this->StoreReuseWastageModel;
                        $plan_id = $collection->id;
                        $collectionReuse = self::_storeOrUpdateReuseWastage($collectionReuse,$request,$plan_id);
                    }
                    
                    if(!empty($collectionReuse))
                    {   
                        $batchWiseMaterial = array();
                        foreach ($request->wastage as $wastage) 
                        {
                            if( !empty($wastage['batch_id']) && !empty($wastage['material_id']) && !empty($wastage['quantity']) ){

                                $wastage_stock_material = explode("||", $wastage['material_id']);
                                $wastage_stock_id = $wastage_stock_material[0];
                                $wastage_material_id = $wastage_stock_material[1];

                                $batchWiseMaterial[$wastage['batch_id']]['reuse_wastage_id']=$collectionReuse->id;
                                $batchWiseMaterial[$wastage['batch_id']]['batch_id']=$wastage['batch_id'];
                                $batchWiseMaterial[$wastage['batch_id']]['waste_stock_id']=$wastage_stock_id;
                                $batchWiseMaterial[$wastage['batch_id']][$this->wastageMaterialRecords[$wastage_material_id]]=$wastage['quantity'];
                            }

                        }
                        
                        if(!empty($batchWiseMaterial)){
                            foreach ($batchWiseMaterial as $wastageMaterial) {

                                $wastageMaterialObj = new $this->StoreReuseWastageHasMaterialsModel;
                                $wastageMaterialObj->reuse_wastage_id = $wastageMaterial['reuse_wastage_id'];
                                $wastageMaterialObj->batch_id = $wastageMaterial['batch_id'];
                                $wastageMaterialObj->waste_stock_id   =  $wastageMaterial['waste_stock_id'];
                                $wastageMaterialObj->course   = !empty($wastageMaterial['Course']) ? $wastageMaterial['Course'] : 0;
                                $wastageMaterialObj->rejection   = !empty($wastageMaterial['Rejection']) ? $wastageMaterial['Rejection'] : 0;
                                $wastageMaterialObj->dust   = !empty($wastageMaterial['Dust']) ? $wastageMaterial['Dust'] : 0;
                                $wastageMaterialObj->loose   = !empty($wastageMaterial['Loose']) ? $wastageMaterial['Loose'] : 0;

                                if ($wastageMaterialObj->save()) 
                                {
                                    $all_transactions[] = 1;

                                    $storeWasteStock = $this->StoreWasteStockModel
                                                        ->find($wastageMaterial['waste_stock_id']);
                                    $storeWasteStock->balance_course = $storeWasteStock->balance_course - $wastageMaterialObj->course;   
                                    $storeWasteStock->balance_rejection = $storeWasteStock->balance_rejection - $wastageMaterialObj->rejection;
                                    $storeWasteStock->balance_dust = $storeWasteStock->balance_dust - $wastageMaterialObj->dust;    
                                    $storeWasteStock->balance_loose =$storeWasteStock->balance_loose - $wastageMaterialObj->loose; 

                                    ##Update Balance qty of materials in store_waste_stock
                                    if($storeWasteStock->save()){
                                        $all_transactions[] = 1;
                                    }else{
                                        $all_transactions[] = 0;
                                    }  

                                }else{
                                    $all_transactions[] = 0;
                                }
                            }
                        }

                    }

                }



                if (!in_array(0,$all_transactions)) 
                {
                    $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                    $this->JsonData['url'] = route($this->ModulePath.'index');
                    $this->JsonData['msg'] = 'Production Plan added successfully.';
                    DB::commit();
                }               
            } 
            else
            {
                DB::rollback();
            }
        }
        catch(\Exception $e) {
            $this->JsonData['error_msg'] = $e->getMessage();
            DB::rollback();
        }
        return response()->json($this->JsonData);
    }  

    public function edit($encID)
    {
        /* ASHVINI */        
        /* END ASHVINI */
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['modulePath']   = $this->ModulePath;
        $id = base64_decode(base64_decode($encID));
        $companyId = self::_getCompanyId();        
        //dd($id );
        $data = $this->BaseModel
        ->with([   
            'hasProductionMaterials' => function($q)
            {  
                $q->with('mateialName');
                $q->with('hasLot');
            },
            'assignedBatch' => function($q){
                $q->with('assignedProduct');
            },
            'hasReuseWastage' => function($q)
            {  
               $q->with(['hasReuseMaterials'=>function($q1){
                    $q1->with(['assignedBatch','assignedWastageStock']); 
               }]);
            }

        ])
        ->where('company_id', $companyId)
        ->find($id);
        
        /*if(empty($data) || $data->assignedBatch->review_status == 'closed') {            
            return redirect()->route('admin.production.index');
        }*/
        /*$objStore = new StoreBatchCardModel();
        $batchNos = $objStore->getBatchNumbers($companyId);
        $this->ViewData['batchNos']   = $batchNos;*/

        $batchesHtml = self::_getWastageBatch($data->assignedBatch->product_code,$companyId);
       // dd($batchesHtml);
        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getLotMaterials($companyId);

        $rcester_companyId = config('constants.RCESTERCOMPANY');
        $rcEsterCompany = false;
        if($companyId==$rcester_companyId){
            $rcEsterCompany = true;
           // $this->wastageMaterialRecords[0] = 'Unfiltered';
        }

        ## VIEW FILE WITH DATA
        $this->ViewData['materialIds']   = $materialIds;        
        $this->ViewData['production'] = $data;
        $this->ViewData['wastages'] = $this->wastageMaterialRecords;
        $this->ViewData['batchesHtml'] = $batchesHtml;
        $this->ViewData['rcEsterCompany'] = $rcEsterCompany;
        //dd($this->ViewData);

        
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreProductionRequest $request, $encID)    
    {
        //dd($request->all());
        ##Validation for Wastage Material Stock quantity
        if (!empty($request->wastage) && count($request->wastage) > 0){
            foreach ($request->wastage as $wastage){
                if( !empty($wastage['batch_id']) && !empty($wastage['material_id']) && !empty($wastage['quantity']) ){

                    if($wastage['quantity']<=0){

                        $this->JsonData['status'] = __('admin.RESP_ERROR');
                        $this->JsonData['msg'] = 'You cannot add quantity less than one'; 
                        return response()->json($this->JsonData);
                        exit();
                    }
                    if($wastage['quantity']>$wastage['wastageQuantityLimit']){

                        $this->JsonData['status'] = __('admin.RESP_ERROR');
                        $this->JsonData['msg'] = 'You can not select more than available quantity:'.$wastage['wastageQuantityLimit']; 
                        return response()->json($this->JsonData);
                        exit();
                    }
                }
            }
        }

        DB::beginTransaction();
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Branch, Something went wrong on server.';       

        $id = base64_decode(base64_decode($encID));
        $companyId = self::_getCompanyId();
        try {

            $collection = $this->BaseModel->find($id);
            $batchId = $collection->batch_id;
            $cDate = $collection->created_at;
            //dd($cDate);            
            $collection = self::_storeOrUpdate($collection,$request);
            if($collection->save())
            {
                $all_transactions = [];
                $productionId = $id;
                if (!empty($request->production) && sizeof($request->production) > 0) 
                {                     
                    ## GET PREIOUS LOT QUANTITIES
                    $prodRawMaterialModel = new ProductionHasMaterialModel;
                    $prevRecords = $prodRawMaterialModel->where('production_id',$productionId)->get(['material_id','lot_id','quantity','created_at'])->toArray();
                    
                    $result = array();
                    ## IF Duplicate row for same material and lot id
                    # MAKE addition of quantities and create one record
                    foreach($request->production as $val){
                        if(isset($result[$val['material_id']][$val['lot_id']])){
                            $result[$val['material_id']][$val['lot_id']] = $result[$val['material_id']][$val['lot_id']] + $val['quantity'];
                        }
                        else {
                            $result[$val['material_id']][$val['lot_id']] = $val['quantity'];
                        }
                        
                    }

                    $finalArray = $correntRecords = $crntOpeningRecs = array();
                    $i = 0;
                    foreach($result as $materialId=>$rVal){     
                        foreach($rVal as $lotId=>$quantity){
                            if($quantity<=0){
                                $this->JsonData['status'] = __('admin.RESP_ERROR');
                                $this->JsonData['msg'] = 'You cannot add quantity less than one'; 
                                DB::rollback();
                                return response()->json($this->JsonData);
                                exit();
                            }    
                            if($quantity > 0 && $materialId > 0 && $lotId > 0){
                                $finalArray[$i]['production_id'] = $productionId;
                                $finalArray[$i]['material_id'] = $materialId;
                                $finalArray[$i]['lot_id'] = $lotId;
                                $finalArray[$i]['quantity'] = $quantity;
                                $finalArray[$i]['created_at'] = $cDate;
                                /*if(isset($datesArr[$lotId]) && $datesArr[$lotId] != '')
                                    $finalArray[$i]['created_at'] = $datesArr[$lotId];
                                else{                                    
                                    $finalArray[$i]['created_at'] = Carbon::now()->toDateTimeString();
                                }*/
                                $i++;
                                $correntRecords[$lotId] = $quantity;
                                $crntOpeningRecs[$materialId][$lotId] = floatval($quantity);
                            }                            
                        }   
                    }

                    if(!empty($finalArray)){
                        //$detailIds = array_column($request->production, 'id');
                        $prodRawMaterialObj = new ProductionHasMaterialModel;
                        $prodRawMaterialObj->where('production_id', $productionId)->delete();

                        $prodRawMaterialObj1 = new ProductionHasMaterialModel;
                        $prodRawMaterialObj1->insert($finalArray);
                        
                        ## ADD BALANCE In MATERIAL IN
                        $prevOpeningRecs = array();
                        foreach($prevRecords as $pKey=>$pVal){
                            ## GET PREVIOUS LOT USED DATE
                            $lotDetails = $prodRawMaterialObj1->selectRaw('created_at')->where('lot_id', $pVal['lot_id'])->orderBy('store_production_has_materials.id', 'DESC')->first();
                            $prevDate = null;
                            if($lotDetails)
                                $prevDate = $lotDetails->created_at;
                            
                            $inObj = new StoreInMaterialModel;
                            $inMaterialcollection = $inObj->find($pVal['lot_id']);
                            $updateBal = $inObj->updateBalance($inMaterialcollection, $pVal['quantity'], true, $prevDate);
                            $prevOpeningRecs[$pVal['material_id']][$pVal['lot_id']] = $pVal['quantity'];
                            if($updateBal) 
                            {                            
                                $all_transactions[] = 1;
                            } else {
                                $all_transactions[] = 0;
                            }
                        }
                        ## REMOVE THE NEW PLANNED QUANTITY FROM STOCK
                        foreach($correntRecords as $cLotId=>$cQuantity){
                            $inObj = new StoreInMaterialModel;
                            $inMaterialcollection = $inObj->find($cLotId);
                            $updateBal = $inObj->updateBalance($inMaterialcollection, $cQuantity);
                            if($updateBal) 
                            {                            
                                $all_transactions[] = 1;
                            } else {
                                $all_transactions[] = 0;
                            }
                        }
                        ## UPDATE MATERIAL OPENING BALANCE
                        $todaysDate =  Carbon::today()->format('Y-m-d');
                        if($cDate < $todaysDate){
                           $objMattOpen = new StoreMaterialOpeningModel;
                           /*$objMattOpen->updateOpeningBals($prevOpeningRecs, $crntOpeningRecs);*/
                           $objMattOpen->updateOpeningBalsNew($cDate, $prevOpeningRecs, $crntOpeningRecs);
                        }
                        ## END UPDATE MATERIAL OPENING BALANCE

                        ## UPDATE LOSS MATERIAL AND YIELD
                        $materialOutObj = new StoreOutMaterialModel;
                        $outputRec = $materialOutObj->getOutputRec($productionId);
                        if($outputRec){                            
                            $outPutId =  $outputRec->id;
                            $rcester_companyId = config('constants.RCESTERCOMPANY');
                            if($companyId==$rcester_companyId){
                                $updateOutput = $materialOutObj->rcUpdateMadeByMaterial($outPutId, $companyId);   
                            } else {
                               $updateOutput = $materialOutObj->updateMadeByMaterial($outPutId, $companyId); 
                            }
                            
                            if($updateOutput) 
                            {                            
                                $all_transactions[] = 1;
                            } else {
                                $all_transactions[] = 0;
                            }
                        }

                        if (!empty($request->wastage) && count($request->wastage) > 0) 
                        {
                            /*$rcester_companyId = config('constants.RCESTERCOMPANY');
                            if($companyId==$rcester_companyId){
                                $this->wastageMaterialRecords[0] = 'Unfiltered';
                            }*/
                            if( !empty($request->wastage[0]['batch_id']) && !empty($request->wastage[0]['material_id']) && !empty($request->wastage[0]['quantity']) )
                            {
                                $reuse_wastage_id = $request->reuse_wastage_id;
                                $plan_id = $productionId;
                                $collectionReuse = $this->StoreReuseWastageModel->find($reuse_wastage_id);
                                // dd($collectionReuse);
                                $collectionReuse = self::_storeOrUpdateReuseWastage($collectionReuse,$request,$plan_id);
                            }
                            
                            if(!empty($collectionReuse))
                            {   
                                $batchWiseMaterial = array();
                                foreach ($request->wastage as $wastage) 
                                {
                                    if( !empty($wastage['batch_id']) && !empty($wastage['material_id']) && !empty($wastage['quantity']) ){

                                        $wastage_stock_material = explode("||", $wastage['material_id']);
                                        $wastage_stock_id = $wastage_stock_material[0];
                                        $wastage_material_id = $wastage_stock_material[1];

                                        $batchWiseMaterial[$wastage['batch_id']]['reuse_wastage_id']=$collectionReuse->id;
                                        $batchWiseMaterial[$wastage['batch_id']]['batch_id']=$wastage['batch_id'];
                                        $batchWiseMaterial[$wastage['batch_id']]['waste_stock_id']=$wastage_stock_id;
                                        $batchWiseMaterial[$wastage['batch_id']][$this->wastageMaterialRecords[$wastage_material_id]]=$wastage['quantity'];
                                    }

                                }
                                
                                if(!empty($batchWiseMaterial)){
                                    //Update store_waste_stock and Delete the records of store_reuse_wastage_has_materials

                                    $oldwastageMaterials = $this->StoreReuseWastageHasMaterialsModel
                                        ->where('reuse_wastage_id',$reuse_wastage_id)
                                        ->get();

                                    foreach ($oldwastageMaterials as $oldwastageMaterial) {

                                        $storeWasteStockRec = $this->StoreWasteStockModel
                                                                    ->find($oldwastageMaterial->waste_stock_id);
                                        
                                        $storeWasteStockRec->balance_course = $storeWasteStockRec->balance_course + $oldwastageMaterial->course;

                                        $storeWasteStockRec->balance_rejection = $storeWasteStockRec->balance_rejection + $oldwastageMaterial->rejection;
                                        
                                        $storeWasteStockRec->balance_dust = $storeWasteStockRec->balance_dust + $oldwastageMaterial->dust;
                                       
                                        $storeWasteStockRec->balance_loose = $storeWasteStockRec->balance_loose + $oldwastageMaterial->loose;

                                        if($storeWasteStockRec->save()){
                                            $all_transactions[] = 1;
                                        }else{
                                            $all_transactions[] = 0;
                                        }  

                                    }

                                    //Delete records
                                    $this->StoreReuseWastageHasMaterialsModel->where('reuse_wastage_id', $reuse_wastage_id)->delete();


                                    foreach ($batchWiseMaterial as $wastageMaterial) {

                                        $wastageMaterialObj = new $this->StoreReuseWastageHasMaterialsModel;
                                        $wastageMaterialObj->reuse_wastage_id = $wastageMaterial['reuse_wastage_id'];
                                        $wastageMaterialObj->batch_id = $wastageMaterial['batch_id'];
                                        $wastageMaterialObj->waste_stock_id   =  $wastageMaterial['waste_stock_id'];
                                        $wastageMaterialObj->course   = !empty($wastageMaterial['Course']) ? $wastageMaterial['Course'] : 0;
                                        $wastageMaterialObj->rejection   = !empty($wastageMaterial['Rejection']) ? $wastageMaterial['Rejection'] : 0;
                                        $wastageMaterialObj->dust   = !empty($wastageMaterial['Dust']) ? $wastageMaterial['Dust'] : 0;
                                        $wastageMaterialObj->loose   = !empty($wastageMaterial['Loose']) ? $wastageMaterial['Loose'] : 0;

                                        if ($wastageMaterialObj->save()) 
                                        {
                                            $all_transactions[] = 1;

                                            $storeWasteStock = $this->StoreWasteStockModel
                                                                ->find($wastageMaterial['waste_stock_id']);
                                            $storeWasteStock->balance_course = $storeWasteStock->balance_course - $wastageMaterialObj->course;   
                                            $storeWasteStock->balance_rejection = $storeWasteStock->balance_rejection - $wastageMaterialObj->rejection;
                                            $storeWasteStock->balance_dust = $storeWasteStock->balance_dust - $wastageMaterialObj->dust;    
                                            $storeWasteStock->balance_loose =$storeWasteStock->balance_loose - $wastageMaterialObj->loose; 

                                            ##Update Balance qty of materials in store_waste_stock
                                            if($storeWasteStock->save()){
                                                $all_transactions[] = 1;
                                            }else{
                                                $all_transactions[] = 0;
                                            }  

                                        }else{
                                            $all_transactions[] = 0;
                                        }
                                    }
                                }

                            }

                        }
                    ## UPDATE COST PER UNIT AFTER EDIT CLOSED BATCH                     
                    $objBatchCard = new StoreBatchCardModel;
                    $objBatchCard->updateClosedBatch($batchId, true);

                    } else {
                        $all_transactions[] = 0;
                    }
                   
                }
                if (!in_array(0,$all_transactions)) 
                {
                    $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                    $this->JsonData['url'] = route($this->ModulePath.'index');
                    $this->JsonData['msg'] = $this->ModuleTitle.' Updated successfully.';
                    DB::commit();
                }                
            } else {
                DB::rollback();
            }
        }
        catch(\Exception $e) {
            $this->JsonData['msg'] = $e->getMessage();
        }
        return response()->json($this->JsonData);
    }

    public function destroy($encID)
    {
        
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to delete production, Something went wrong on server.';
        $id = base64_decode(base64_decode($encID));

        $available_count = $this->StoreOutMaterialModel->where('plan_id',$id)->count();
        if($available_count>0) 
        {
            $this->JsonData['status'] = __('admin.RESP_ERROR');
            $this->JsonData['msg'] = 'Cant delete this Production which is assigned in Material Output Module'; 
            return response()->json($this->JsonData);
            exit();
        }

        $available_count = $this->StoreReturnedMaterialModel->where('plan_id',$id)->count();
        if($available_count>0) 
        {
            $this->JsonData['status'] = __('admin.RESP_ERROR');
            $this->JsonData['msg'] = 'Cant delete this Production which is assigned in Returned Material Module'; 
            return response()->json($this->JsonData);
            exit();
        }

        DB::beginTransaction();

        $BaseModel = $this->BaseModel->find($id);
        $batchId = $BaseModel->batch_id;
        $cDate = $BaseModel->created_at;
        $prodRawMaterialModel = new ProductionHasMaterialModel;
        $prevRecords = $prodRawMaterialModel->where('production_id',$id)->get(['material_id','lot_id','quantity'])->toArray();
        $all_transactions = [];
        try {
            if($BaseModel->delete())
            {
                $prodRawMaterialModel->where('production_id', $id)->delete();
                $inObj = new StoreInMaterialModel;
                $prevOpeningRecs = array();
                foreach($prevRecords as $pKey=>$pVal){                
                    ## GET PREVIOUS LOT USED DATE
                    $lotDetails = $prodRawMaterialModel->selectRaw('created_at')->where('lot_id', $pVal['lot_id'])->orderBy('store_production_has_materials.id', 'DESC')->first();
                    $prevDate = null;
                    if($lotDetails)
                        $prevDate = $lotDetails->created_at;
                    $inMaterialcollection = $inObj->find($pVal['lot_id']);
                    $updateBal = $inObj->updateBalance($inMaterialcollection, $pVal['quantity'], true, $prevDate);
                    $prevOpeningRecs[$pVal['material_id']][$pVal['lot_id']] = $pVal['quantity'];
                    if($updateBal) 
                    {                            
                        $all_transactions[] = 1;
                    } else {
                        $all_transactions[] = 0;
                    }
                }
                ## UPDATE MATERIAL OPENING BALANCE
                $todaysDate =  Carbon::today()->format('Y-m-d');
                if($cDate < $todaysDate){
                   $objMattOpen = new StoreMaterialOpeningModel;
                   /*$objMattOpen->updateOpeningBals($prevOpeningRecs);*/
                   $objMattOpen->updateOpeningBalsNew($cDate, $prevOpeningRecs);
                }

                ## MARK BATCH AS PLAN ADDED BATCH
                $objBatch = new StoreBatchCardModel();
                $objBatch->updatePlanAdded($batchId, 'no');     

                ## UPDATE WASTAGE STOCK AND DELETE THE REUSE WASTAGE RECORD
                $oldwastageMaterials = $this->StoreReuseWastageModel
                                        ->join('store_reuse_wastage_has_materials','store_reuse_wastage_has_materials.reuse_wastage_id','=','store_reuse_wastage.id')
                                        ->where('store_reuse_wastage.plan_id',$id)
                                        ->get(['reuse_wastage_id','waste_stock_id','course','rejection','dust','loose']);

                if(!empty($oldwastageMaterials) && count($oldwastageMaterials)>0){

                    foreach ($oldwastageMaterials as $oldwastageMaterial) {

                        $reuse_wastage_id = $oldwastageMaterial->reuse_wastage_id;
                        $storeWasteStockRec = $this->StoreWasteStockModel
                                                    ->find($oldwastageMaterial->waste_stock_id);
                        
                        $storeWasteStockRec->balance_course = $storeWasteStockRec->balance_course + $oldwastageMaterial->course;

                        $storeWasteStockRec->balance_rejection = $storeWasteStockRec->balance_rejection + $oldwastageMaterial->rejection;
                        
                        $storeWasteStockRec->balance_dust = $storeWasteStockRec->balance_dust + $oldwastageMaterial->dust;
                       
                        $storeWasteStockRec->balance_loose = $storeWasteStockRec->balance_loose + $oldwastageMaterial->loose;

                        if($storeWasteStockRec->save()){
                            $all_transactions[] = 1;
                        }else{
                            $all_transactions[] = 0;
                        }  

                    }

                    //Delete records
                    $this->StoreReuseWastageModel->where('id', $reuse_wastage_id)->delete();
                    $this->StoreReuseWastageHasMaterialsModel->where('reuse_wastage_id', $reuse_wastage_id)->delete();
                }                        



            } else {
                $all_transactions[] = 0;
            }
            if (!in_array(0,$all_transactions)) 
            {
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route($this->ModulePath.'index');
                $this->JsonData['msg'] = $this->ModuleTitle.' deleted successfully.';
                DB::commit();
            } else {
                DB::rollback();
            }

        } catch(\Exception $e) {
            $this->JsonData['error_msg'] = $e->getMessage();
            DB::rollback();
        }
        return response()->json($this->JsonData);
    }

    public function _storeOrUpdate($collection, $request)
    {
        $collection->company_id        = self::_getCompanyId();
        $collection->user_id        = auth()->user()->id;
        $collection->batch_id        = $request->batch_id;        
        $collection->status             = !empty($request->status) ? 1 : 0;
        
        ## SAVE DATA
        //$collection->save();        
        return $collection;
    }

    public function _storeOrUpdateReuseWastage($collection, $request,$plan_id)
    {
        $collection->company_id  = self::_getCompanyId();
        $collection->user_id     = auth()->user()->id;
        $collection->plan_id     = $plan_id;
        $collection->batch_id    = $request->batch_id;        
        
        ## SAVE DATA
        $collection->save();        
        return $collection;
    }
    public function show($encId)
    {
        $id = base64_decode(base64_decode($encId));
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;
        $this->ViewData['object'] = $this->BaseModel
        ->with([   
            'hasProductionMaterials' => function($q)
            {  
                $q->with('mateialName');
                $q->with('hasLot');
            }
        ])->with(['assignedBatch' => function($q){
                $q->with('assignedProduct');
        }
        ])
        ->find($id);         
        //->find($id)->toArray(); //
        //dd($this->ViewData['object']);
        return view($this->ModuleView.'view', $this->ViewData);
    }
    public function getRecords(Request $request)
    {
		//dd($request->all());
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
            0 => 'store_productions.id',
            1 => 'store_batch_cards.batch_card_no',
            2 => 'products.code',
            3 => 'total_qty', 
            4 => 'review_status',                   
        );       

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/
        $companyId = self::_getCompanyId();
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_productions.id, store_productions.batch_id,  store_batch_cards.batch_card_no, store_batch_cards.review_status, products.name, products.code, SUM(store_production_has_materials.quantity) as total_qty')
        ->leftjoin('store_production_has_materials', 'store_production_has_materials.production_id' , '=', 'store_productions.id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_productions.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
        ->leftjoin('store_raw_materials', 'store_raw_materials.id' , '=', 'store_production_has_materials.material_id')     
        ->where('store_productions.company_id', $companyId)
        ->where('store_raw_materials.material_type', 'Raw');

        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();
        
        //dd($request->custom);
        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['batch_id'])) 
            {
                $custom_search = true;
                $key = $request->custom['batch_id'];                
                $modelQuery = $modelQuery
                ->where('store_productions.batch_id', $key);
            }

            if (!empty($request->custom['product_code'])) 
            {
                $custom_search = true;
                $key = $request->custom['product_code'];               
                $modelQuery = $modelQuery
                ->where('store_batch_cards.product_code',  $key);               
            }
            if (isset($request->custom['review_status'])) 
            {
                $custom_search = true;
                $key = $request->custom['review_status'];
                $modelQuery = $modelQuery
                ->where('store_batch_cards.review_status', $key);
            }
        }

        if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];
                
                $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('products.name', 'LIKE', '%'.$search.'%');
                    $query->orwhere('products.code', 'LIKE', '%'.$search.'%');
                });
            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_batch_cards.review_status', 'ASC')->orderBy('store_productions.id', 'DESC'); 
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
       /* $object = $modelQuery->skip($start)
        ->take($length)
        ->groupBy('store_production_has_materials.production_id')
        ->havingRaw('sum(store_production_has_materials.quantity)>6' )
        ->get(); */ 
         $modelQuery =  $modelQuery->skip($start)
        ->take($length)
        ->groupBy('store_production_has_materials.production_id');

        if (!empty($request->custom['quantity'])) 
        {
            $custom_search = true;
            $key = $request->custom['quantity'];                
            $modelQuery = $modelQuery
            ->havingRaw('sum(store_production_has_materials.quantity) > '.$key );
        }

         $object = $modelQuery
         ->get(); 

        //dd($object);
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_productions[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['batch_id']  = $row->batch_card_no;
                $data[$key]['product_code']  =  $row->code." ( ".$row->name." )";
                $data[$key]['quantity']  =  number_format($row->total_qty, 2, '.','');
                if( $row->review_status == 'open'){
                    $data[$key]['review_status'] = 'Open';
                } else {
                    $data[$key]['review_status'] = 'Closed';
                }              
                $edit = $delete = '';
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';              
                if( $row->review_status == 'open'){                    
                     $delete = '<a href="javascript:void(0)" onclick="return deleteCollection(this)" data-href="'.route($this->ModulePath.'destroy', [base64_encode(base64_encode($row->id))]) .'" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>';    
                }
                
                $view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'" title="View"><span class="glyphicon glyphicon-eye-open"></a>';

                $data[$key]['actions'] = '';
                $data[$key]['actions'] =  '<div class="text-center">'.$view.'</div>';
                if(auth()->user()->can('store-material-plan-add'))
                {
                    $data[$key]['actions'] =  '<div class="text-center">'.$view.' '.$edit.' '.$delete.'</div>';
                }

         }
     }
    $objStore = new StoreBatchCardModel;
    $batchNos = $objStore->getBatchNumbers($companyId);

    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
     
    $batch_no_string = '<select name="batch_no" id="batch-id" class="form-control my-select select2"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_id'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';

    $objProduct = new ProductsModel;
    $products = $objProduct->getProducts($companyId);
    $product_code_string = '<select name="product_code" id="product-code" class="form-control my-select select2"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product_code'] == $product['id'] ? 'selected' : '').' >'.$product['code'].' ('.$product['name'].' )</option>';
        }
    $product_code_string .='</select>';
    $searchHTML['batch_id'] = $batch_no_string;
    $searchHTML['product_code'] = $product_code_string;
    $searchHTML['quantity']     =  '<input type="text" class="form-control" id="quantity" value="'.($request->custom['quantity']).'" placeholder="More than...">';
    $searchHTML['review_status']   =  '<select name="review_status" id="review-status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Batch Status</option>
            <option class="theme-black blue-select" value="open" '.( $request->custom['review_status'] == "open" ? 'selected' : '').' >Open</option>
            <option class="theme-black blue-select" value="closed" '.( $request->custom['review_status'] == "closed" ? 'selected' : '').'>Closed</option>            
            </select>';  
    // if ($custom_search) 
    // {
    //     $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return removeSearch(this)" class="btn btn-danger"><span class="fa  fa-remove"></span></a></div>';
    // }
    // else
    // {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';
    /*}*/
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
        DB::beginTransaction();
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to delete batch, Something went wrong on server.';
        try {
            if (!empty($request->arrEncId)) 
            {
                $all_transactions = [];
                $arrID = array_map(function($item)
                {
                    return base64_decode(base64_decode($item));

                }, $request->arrEncId);
                $prodRawMaterialModel = new ProductionHasMaterialModel;
                foreach($arrID as $id){                    
                    $prevRecords = $prodRawMaterialModel->where('production_id',$id)->get(['lot_id','quantity'])->toArray();
                    if($this->BaseModel->where('id', $id)->delete()){
                        //$prodRawMaterialObj = new ProductionHasMaterialModel;
                        $prodRawMaterialModel->where('production_id', $id)->delete();
                         ## ADD BALANCE In MATERIAL IN
                        foreach($prevRecords as $pKey=>$pVal){
                            $inObj = new StoreInMaterialModel;
                            $inMaterialcollection = $inObj->find($pVal['lot_id']);
                            $updateBal = $inObj->updateBalance($inMaterialcollection, $pVal['quantity'], true);
                            if($updateBal) 
                            {                            
                                $all_transactions[] = 1;
                            } else {
                                $all_transactions[] = 0;
                            }
                        }

                    } else {
                        $all_transactions[] = 0;
                    }
                }
                if (!in_array(0,$all_transactions)) 
                {
                    $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                    $this->JsonData['url'] = route($this->ModulePath.'index');
                    $this->JsonData['msg'] = $this->ModuleTitle.' deleted successfully.';
                    DB::commit();
                }     

            } else {
                DB::rollback();
            }

        } catch(\Exception $e) {
            $this->JsonData['error_msg'] = $e->getMessage();
            DB::rollback();
        }       

       return response()->json($this->JsonData);   
    }

    public function getBatchMaterials(Request $request)
    {
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get batch materials, Something went wrong on server.';
        try 
        {
            $material_id   = $request->material_id;
            $batch_id   = $request->batch_id;
            
            if(!empty($material_id)){
                $html       = self::_getBatchMaterials($batch_id,$material_id);
            }else{
                $html       = self::_getBatchMaterials($batch_id);
            }
 
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
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get material Lots, Something went wrong on server.';
        try 
        {
            $material_id   = $request->material_id;
            $selected_val      = $request->selected_val;    
            $html = "";  
            if(!empty($material_id)){
                $html       = self::_getMaterialLots($material_id, $selected_val);
            }
 
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

    public function getExistingBatch(Request $request)
    {
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get material Lots, Something went wrong on server.';
        try 
        {
            $batch_id   = $request->batch_id;
            $collection = $this->BaseModel->where('batch_id',$batch_id)->first();
            
            // $product_id = $request->product_id;
            $company_id = self::_getCompanyId();

            $url = '';
            if($collection){               
                $url = route($this->ModulePath.'edit', [ base64_encode(base64_encode($collection->id))]);    
            }
            $this->JsonData['url']  = $url;
            //$this->JsonData['product']  = $product;
            $this->JsonData['msg']  = 'Raw Materials';
            $this->JsonData['status']  = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }


    public function getWastageBatchesOrMaterials(Request $request)
    {
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get wastage batches, Something went wrong on server.';
        try 
        {
            $flag               = $request->flag;
            $batch_id           = $request->batch_id;
            $wastage_batch_id   = $request->wastage_batch_id;
            

            $product_id         = $request->product_id;
            $company_id         = self::_getCompanyId();
            $batchesHtml="<option value=''>Select Batch</option>";
            $batchesMaterialHtml="<option value=''>Select Material</option>";

            if($flag=="loadbatch")
            {

                $batchesHtml = self::_getWastageBatch($product_id,$company_id);
                
            }elseif($flag=="loadmaterial"){

                $batch_selected_val     = $request->batch_selected_val;
                $material_selected_val  = $request->material_selected_val;

                array_pop($batch_selected_val);
            
               // $batch_material = array_combine($batch_selected_val,$material_selected_val);

                $batch_material = array();
                foreach ($batch_selected_val as $batch_key=>$batch_value) {
                    if(!empty($material_selected_val)){
                        $batch_material[$batch_value][]=$material_selected_val[$batch_key];
                    }
                }
            
                //dd($batch_selected_val,$material_selected_val,$batch_material);
                $rcester_companyId = config('constants.RCESTERCOMPANY');
                $companyCourseOrUnfiltered = 'Course';
                if($company_id==$rcester_companyId){
                    $this->wastageMaterialRecords[0] = 'Unfiltered';
                    $companyCourseOrUnfiltered = 'Unfiltered';
                }

                $wastageBatchesMaterials = $this->StoreWasteStockModel
                            ->where('store_waste_stock.product_id',$product_id)
                            ->where('store_waste_stock.batch_id',$wastage_batch_id)
                            ->where('store_waste_stock.company_id',$company_id)
                            ->get([
                                    'store_waste_stock.id as waste_stock_id',
                                    'store_waste_stock.balance_course as '.$companyCourseOrUnfiltered,
                                    'store_waste_stock.balance_rejection as Rejection',
                                    'store_waste_stock.balance_dust as Dust',
                                    'store_waste_stock.balance_loose as Loose',
                                ]);

                
                //dd($wastageBatchesMaterials,$this->wastageMaterialRecords,$batch_material);

                foreach($wastageBatchesMaterials as $batchMaterial){

                    foreach ($this->wastageMaterialRecords as $materialKey=>$material)
                    {
                        // dd($material,$batchMaterial->$material);
                        if($batchMaterial->$material>0){
                            // !empty($batch_material[$wastage_batch_id])
                            if(array_key_exists($wastage_batch_id,$batch_material) && !in_array($materialKey, $batch_material[$wastage_batch_id]) ){

                                $batchesMaterialHtml.="<option data-qty='".$batchMaterial->$material."' value='".$batchMaterial->waste_stock_id."||".$materialKey."'>".$material." (".$batchMaterial->$material.")</option>";
                            }
                            elseif(!array_key_exists($wastage_batch_id,$batch_material)){
                                 $batchesMaterialHtml.="<option data-qty='".$batchMaterial->$material."' value='".$batchMaterial->waste_stock_id."||".$materialKey."'>".$material." (".$batchMaterial->$material.")</option>";
                            }
                            
                        }
                    }

                }
            }

            // dd($wastageBatchesMaterials->toArray(),$batchesMaterialHtml,$batchesHtml);
           
            $this->JsonData['batchesHtml']  = $batchesHtml;
            $this->JsonData['batchesMaterialHtml']  = $batchesMaterialHtml;
            $this->JsonData['msg']      = 'Batches and its Material';
            $this->JsonData['status']   = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }


    public function _getWastageBatch($product_id,$company_id){

        $rcester_companyId = config('constants.RCESTERCOMPANY');
        $companyCourseOrUnfiltered = 'Course';
        if($company_id==$rcester_companyId){
            $companyCourseOrUnfiltered = 'Unfiltered';
        }

        $wastageBatchesMaterials = $this->StoreBatchCardModel
                            ->join('store_waste_stock','store_waste_stock.batch_id','=','store_batch_cards.id')
                            ->where('store_waste_stock.product_id',$product_id)
                            ->where('store_waste_stock.company_id',$company_id)
                            ->get([
                                    'store_batch_cards.id',
                                    'store_batch_cards.batch_card_no',
                                    'store_waste_stock.id as waste_stock_id',
                                    'store_waste_stock.balance_course as '.$companyCourseOrUnfiltered,
                                    'store_waste_stock.balance_rejection as Rejection',
                                    'store_waste_stock.balance_dust as Dust',
                                    'store_waste_stock.balance_loose as Loose',
                                ]);
        $batchesHtml = "<option value=''>Select Batch</option>";   
        foreach($wastageBatchesMaterials as $batches){
            
            $batchesHtml.= "<option value='".$batches->id."'>".$batches->batch_card_no."</option>";
        }
        return $batchesHtml;
    }

}
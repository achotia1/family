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

use App\Http\Requests\Admin\StoreProductionRequest;
use App\Traits\GeneralTrait;

use DB;
class StoreProductionController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreProductionModel $StoreProductionModel,
        StoreRawMaterialModel $StoreRawMaterialModel
    )
    {
        $this->BaseModel  = $StoreProductionModel;
        $this->StoreProductionModel  = $StoreProductionModel;
        $this->StoreRawMaterialModel  = $StoreRawMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Production';
        $this->ModuleView  = 'admin.store-production.';
        $this->ModulePath = 'admin.production.';

        ## PERMISSION MIDDELWARE
       /* $this->middleware(['permission:manage-batches'], ['only' => ['edit','update','getRecords','bulkDelete']]);
        $this->middleware(['permission:batch-add'], ['only' => ['create','store']]);*/
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
        $objStore = new StoreBatchCardModel();
        $batchNos = $objStore->getBatchNumbers();

        /*$objMaterial = new StoreRawMaterialModel();
        $materialIds = $objMaterial->getMaterialNumbers();*/        
        
        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getLotMaterials($companyId);
        
        /*$objLots = new StoreInMaterialModel;
        $lotIds = $objLots->getBalanceLots(1,$companyId);*/
        
        /*$lotmaterialIds = $objMaterial1->where('status', 1)->with(['hasInMaterials'=> function($q){
                $q->where('status', 1);
                $q->where('lot_qty', '>', 0);                 
            }])
        ->get(['id','name'])->toArray();
        foreach($lotmaterialIds as $mval){
            if(!empty($mval['has_in_materials'])){
                $balanceMaterials[$mval['id']] = $mval['name'];    
            }                      
        }*/
        //dd($lotmaterialIds->toSql());

        //$lotmaterialIds1 = $lotmaterialIds->has('hasInMaterials');
        //dd();
        //dd($materialIds);
        /*$this->BaseModel->where('status', 1)->where('is_reviewed', 'no')->orderBy('id', 'DESC')->with(['assignedProduct'])->get();*/

        $this->ViewData['batchNos']   = $batchNos;
        $this->ViewData['materialIds']   = $materialIds;
        //dd($arrBatchNos);
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreProductionRequest $request)
    {        
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
                    ## ADD IN store_has_production_materials
                    foreach ($request->production as $pkey => $prod) 
                    {
                        $prodRawMaterialObj = new ProductionHasMaterialModel;
                        $prodRawMaterialObj->production_id   = $collection->id;
                        $prodRawMaterialObj->material_id   = !empty($prod['material_id']) ? $prod['material_id'] : NULL;
                        $prodRawMaterialObj->lot_id   =  !empty($prod['lot_id']) ? $prod['lot_id'] : NULL;
                        $prodRawMaterialObj->quantity   = !empty($prod['quantity']) ? $prod['quantity'] : NULL;
                        if ($prodRawMaterialObj->save()) 
                        {                            
                            ## UPDATE LOT QUANTITY                            
                            if($prod['lot_id'] > 0){
                                $inObj = new StoreInMaterialModel;
                                $inMaterialcollection = $inObj->find($prod['lot_id']);
                                $updateBal = $inObj->updateBalance($inMaterialcollection, $prod['quantity']);
                                /*$inMaterialcollection = $inObj->find($prod['lot_id']);
                                $inLotBal = $inMaterialcollection->lot_balance - $prod['quantity'];
                                $inMaterialcollection->lot_balance = $inLotBal;*/

                                if($updateBal) 
                                {
                                    //dd('fgfdg');
                                    $all_transactions[] = 1;
                                } else {
                                    $all_transactions[] = 0;
                                }
                            } else {
                                $all_transactions[] = 0;
                            }
                           
                        }
                        else
                        {
                            $all_transactions[] = 0;
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
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['modulePath']   = $this->ModulePath;
        $id = base64_decode(base64_decode($encID));
        $companyId = self::_getCompanyId();
        
        /*$companyId = self::_getCompanyId();
        $data = $this->BaseModel->where('store_productions.id', base64_decode(base64_decode($encID)))->where('store_productions.company_id', $companyId)->first();
        if(empty($data)) {            
            return redirect()->route('admin.production.index');
        }*/
        $data = $this->BaseModel
        ->with([   
            'hasProductionMaterials' => function($q)
            {  
                $q->with('mateialName');
                $q->with('hasLot');
            }
        ])->with(['assignedBatch' => function($q){
                $q->with('assignedProduct');
        }
        ])->where('company_id', $companyId)
        ->find($id);
        //dd($data);
        if(empty($data)) {            
            return redirect()->route('admin.production.index');
        }
        $objStore = new StoreBatchCardModel();
        $batchNos = $objStore->getBatchNumbers();

        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getLotMaterials($companyId);
        //d($materialIds);
        $this->ViewData['batchNos']   = $batchNos;
        $this->ViewData['materialIds']   = $materialIds;

        /*## ALL DATA
        $this->ViewData['production'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));*/
        $this->ViewData['production'] = $data;
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreProductionRequest $request, $encID)    {
        
        DB::beginTransaction();
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Branch, Something went wrong on server.';       

        $id = base64_decode(base64_decode($encID));
        try {

            $collection = $this->BaseModel->find($id);            
            $collection = self::_storeOrUpdate($collection,$request);
            if($collection->save()){
                $all_transactions = [];
                $productionId = $id;
                if (!empty($request->production) && sizeof($request->production) > 0) 
                { 
                    ## GET PREIOUS LOT QUANTITIES
                    $prodRawMaterialModel = new ProductionHasMaterialModel;
                    $prevRecords = $prodRawMaterialModel->where('production_id',$productionId)->get(['lot_id','quantity'])->toArray();
                    //dd($prevRecords);
                    $result = array();
                    ## IF Duplicate row for same material and lot id
                    # MAKE addition of quantities and create one record
                    //$newQuantities = array();
                    foreach($request->production as $val){
                        if(isset($result[$val['material_id']][$val['lot_id']])){
                            $result[$val['material_id']][$val['lot_id']] = $result[$val['material_id']][$val['lot_id']] + $val['quantity'];
                            //$newQuantities[$val['lot_id']] = 
                        }
                        else {
                            $result[$val['material_id']][$val['lot_id']] = $val['quantity'];
                        }
                        
                    }                   
                    $finalArray = $correntRecords = array();
                    $i = 0;
                    foreach($result as $materialId=>$rVal){     
                        foreach($rVal as $lotId=>$quantity){
                            if($quantity > 0){
                                $finalArray[$i]['production_id'] = $productionId;
                                $finalArray[$i]['material_id'] = $materialId;
                                $finalArray[$i]['lot_id'] = $lotId;
                                $finalArray[$i]['quantity'] = $quantity;
                                $i++;
                                $correntRecords[$lotId] = $quantity;
                            }                            
                        }   
                    }
                    if(!empty($finalArray)){
                        $detailIds = array_column($request->production, 'id');
                        $prodRawMaterialObj = new ProductionHasMaterialModel;
                        $prodRawMaterialObj->whereIn('id', $detailIds)->delete();

                        $prodRawMaterialObj1 = new ProductionHasMaterialModel;
                        $prodRawMaterialObj1->insert($finalArray);
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
                   
                }
                if (!in_array(0,$all_transactions)) 
                {
                    $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                    $this->JsonData['url'] = route($this->ModulePath.'index');
                    $this->JsonData['msg'] = $this->ModuleTitle.' Updated successfully.';
                    DB::commit();
                }
                //dd($request->all());
                /*$this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.production.index');
                $this->JsonData['msg'] = $this->ModuleTitle.' Updated successfully.'; */
            } else {
                DB::rollback();
            }
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);

    }

    public function destroy($id)
    {
        //
    }

    public function _storeOrUpdate($collection, $request)
    {
        $collection->company_id        = self::_getCompanyId();
        $collection->batch_id        = $request->batch_id;        
        $collection->status             = !empty($request->status) ? 1 : 0;
        
        ## SAVE DATA
        //$collection->save();        
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
        /*$filter = array(
            0 => 'store_productions.id',
            1 => 'store_productions.id',
            2 => 'store_productions.batch_id',
            3 => 'store_batch_cards.product_code', 
            4 => 'total_qty',                   
        );*/
        $filter = array(
            0 => 'id',
            1 => 'id',
            2 => 'id',
            3 => 'id', 
            4 => 'id',                   
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/
        $companyId = self::_getCompanyId();
        /*$modelQuery = $this->BaseModel
        ->with([   
            'hasProductionMaterials' => function($q)
            {  
                $q->with('mateialName');
                $q->with('hasLot');
            }
        ])->with(['assignedBatch' => function($q){
                $q->with('assignedProduct');
        }
        ]);*/
        
        /*$modelQuery = $this->BaseModel
        ->with(['assignedBatch' => function($q){
                $q->with('assignedProduct');
        }
        ]);*/
        $modelQuery = $this->BaseModel
        ->with(['assignedBatch.assignedProduct'
        ]);

        ## START MODEL QUERY 
        //$modelQuery = $this->BaseModel->with('hasProductionMaterials');

        /*$modelQuery =  $this->BaseModel        
        ->selectRaw('store_productions.id, store_productions.batch_id,  store_batch_cards.batch_card_no, products.name, products.code, SUM(store_production_has_materials.quantity) as total_qty')
        ->leftjoin('store_production_has_materials', 'store_production_has_materials.production_id' , '=', 'store_productions.id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_productions.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')     
        ->where('store_productions.company_id', $companyId);

        ## GET TOTAL COUNT*/
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
                    ->where('batch_id', $key);
                /*$modelQuery = $modelQuery
                ->where('store_productions.batch_id',  $key);*/

            }

            if (!empty($request->custom['product_code'])) 
            {
                $custom_search = true;
                $key = $request->custom['product_code'];               
                /*$modelQuery = $modelQuery
                ->where('store_batch_cards.product_code',  $key);*/
                $modelQuery = $modelQuery
                    ->whereHas('assignedBatch', function ($query) use ($key) {
                        $query->where('product_code',  $key);
                    });
            }
        }

        if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];
                
                $modelQuery = $modelQuery
                    ->whereHas('assignedBatch', function ($query) use ($search) {
                        $query->where('batch_card_no',  'LIKE', '%'.$search.'%');
                    });

                 /*$modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_batch_cards.product_code', 'LIKE', '%'.$search.'%');
                });*/                
                                

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_productions.status', 'ASC'); 
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
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

                $data[$key]['batch_id']  = $row->assignedBatch->batch_card_no;
                $data[$key]['product_code']  =  $row->assignedBatch->assignedProduct->code." ( ".$row->assignedBatch->assignedProduct->name." )";

                //$row->code." ( ".$row->name." )";
                $data[$key]['quantity']  =  1;//$row->total_qty;                
                               
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';
                $view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'"><img src="'.url('/assets/admin/images').'/icons/eye.svg" alt=" view"></a>';
                $data[$key]['actions'] = '';

                //if(auth()->user()->can('batch-add'))
                //{
                    $data[$key]['actions'] =  '<div class="text-center">'.$edit.' '.$view.'</div>';
                //}

         }
     }
    $objStore = new StoreBatchCardModel;
    $batchNos = $objStore->getBatchNumbers();

    /*$objMaterial = new StoreRawMaterialModel;
    $materialIds = $objMaterial->getMaterialNumbers();*/

    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
     
    $batch_no_string = '<select name="batch_no" id="batch-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_id'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';    

   /* $material_id_string = '<select name="material_id" id="material-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Material</option>';
        foreach ($materialIds as $mval) {
        $material_id_string .='<option class="theme-black blue-select" value="'.$mval['id'].'" '.( $request->custom['material_id'] == $mval['id'] ? 'selected' : '').' >'.$mval['name'].'</option>';
        }
    $material_id_string .='</select>';*/

    $objProduct = new ProductsModel;
    $products = $objProduct->getProducts();
    $product_code_string = '<select name="product_code" id="product-code" class="form-control my-select"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product_code'] == $product['id'] ? 'selected' : '').' >'.$product['code'].' ('.$product['name'].' )</option>';
        }
    $product_code_string .='</select>';
    $searchHTML['batch_id'] = $batch_no_string;
    $searchHTML['product_code'] = $product_code_string;
    $searchHTML['quantity']     =  '';

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
        //dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to delete batch, Something went wrong on server.';

        if (!empty($request->arrEncId)) 
        {
            $arrID = array_map(function($item)
            {
                return base64_decode(base64_decode($item));

            }, $request->arrEncId);

            try 
            {

                if ($this->BaseModel->whereIn('id', $arrID)->delete()) 
                {
                    $this->JsonData['status'] = 'success';
                    $this->JsonData['msg'] = $this->ModuleTitle.' deleted successfully.';
                }

            } catch (Exception $e) 
            {
               $this->JsonData['exception'] = $e->getMessage();
           }
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

}
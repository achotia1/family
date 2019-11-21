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

        

        /*$objStore = new StoreBatchCardModel();
        $batcDetails = $objStore->with(['assignedProduct'])->find(1)->toArray();
        dd($batcDetails);
        die;*/
        $companyId = self::_getCompanyId();
        $objStore = new StoreBatchCardModel();
        $batchNos = $objStore->getBatchNumbers();

        /*$objMaterial = new StoreRawMaterialModel();
        $materialIds = $objMaterial->getMaterialNumbers();*/        
        
        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getLotMaterials($companyId);
        
        /*$d = $objMaterial->find(6)->delete();
        if($d){
            echo "innn";
        } else {
            echo "else";
        }
        dd($d);*/
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
                            if($quantity > 0 && $materialId > 0 && $lotId > 0){
                                $finalArray[$i]['production_id'] = $collection->id;
                                $finalArray[$i]['material_id'] = $materialId;
                                $finalArray[$i]['lot_id'] = $lotId;
                                $finalArray[$i]['quantity'] = $quantity;
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
            //dd($request->all());
            if($collection->save()){
                $all_transactions = [];
                $productionId = $id;
                if (!empty($request->production) && sizeof($request->production) > 0) 
                {                     
                    ## GET PREIOUS LOT QUANTITIES
                    $prodRawMaterialModel = new ProductionHasMaterialModel;
                    $prevRecords = $prodRawMaterialModel->where('production_id',$productionId)->get(['lot_id','quantity'])->toArray();
                    
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
                            if($quantity > 0 && $materialId > 0 && $lotId > 0){
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
                        //$detailIds = array_column($request->production, 'id');
                        $prodRawMaterialObj = new ProductionHasMaterialModel;
                        $prodRawMaterialObj->where('production_id', $productionId)->delete();

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
        $filter = array(
            0 => 'store_productions.id',
            1 => 'store_productions.id',
            2 => 'store_productions.batch_id',
            3 => 'store_batch_cards.product_code', 
            4 => 'total_qty',                   
        );       

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/
        $companyId = self::_getCompanyId();
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_productions.id, store_productions.batch_id,  store_batch_cards.batch_card_no, products.name, products.code, SUM(store_production_has_materials.quantity) as total_qty')
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
            $modelQuery = $modelQuery->orderBy('store_productions.status', 'ASC'); 
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
            ->havingRaw('sum(store_production_has_materials.quantity) = '.$key );
        }

         $object = $modelQuery
         ->get(); 

        //dd($object->toArray());
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
                $data[$key]['quantity']  =  $row->total_qty;                
                               
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

    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
     
    $batch_no_string = '<select name="batch_no" id="batch-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_id'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';

    $objProduct = new ProductsModel;
    $products = $objProduct->getProducts();
    $product_code_string = '<select name="product_code" id="product-code" class="form-control my-select"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product_code'] == $product['id'] ? 'selected' : '').' >'.$product['code'].' ('.$product['name'].' )</option>';
        }
    $product_code_string .='</select>';
    $searchHTML['batch_id'] = $batch_no_string;
    $searchHTML['product_code'] = $product_code_string;
    $searchHTML['quantity']     =  '<input type="text" class="form-control" id="quantity" value="'.($request->custom['quantity']).'" placeholder="Search...">';
      
    if ($custom_search) 
    {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return removeSearch(this)" class="btn btn-danger"><span class="fa  fa-remove"></span></a></div>';
    }
    else
    {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';
    }
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
            $objStore = new StoreBatchCardModel;
            $batcDetails = $objStore->getBatchDetails($batch_id);
            $product = $batcDetails->assignedProduct->code." (".$batcDetails->assignedProduct->name.")";      
            //dd($collection->toArray());
            $url = '';
            if($collection){               
                $url = route($this->ModulePath.'edit', [ base64_encode(base64_encode($collection->id))]);    
            }
            $this->JsonData['url']  = $url;
            $this->JsonData['product']  = $product;
            $this->JsonData['msg']  = 'Raw Materials';
            $this->JsonData['status']  = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }

}
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreOutMaterialModel;
use App\Models\StoreProductionModel;
use App\Models\StoreBatchCardModel;
use App\Models\ProductsModel;
use App\Models\StoreSaleStockModel;

use App\Http\Requests\Admin\StoreOutMaterialRequest;
use App\Traits\GeneralTrait;

use DB;

class StoreOutMaterialController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreOutMaterialModel $StoreOutMaterialModel
    )
    {
        $this->BaseModel  = $StoreOutMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Out Material';
        $this->ModuleView  = 'admin.store-out-material.';
        $this->ModulePath = 'admin.materials-out.';

        ## PERMISSION MIDDELWARE
        $this->middleware(['permission:store-material-output-listing'], ['only' => ['getRecords']]);
        $this->middleware(['permission:store-material-output-add'], ['only' => ['edit','update','create','store','bulkDelete']]);
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
        $objPlan = new StoreProductionModel;
        $plans = $objPlan->getProductionPlans($companyId);
        $this->ViewData['plans']   = $plans;
        
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreOutMaterialRequest $request)
    {        
        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Material, Something went wrong on server.'; 

        try {           
            $collection = new $this->BaseModel;
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                ## CALCULATE LOSS MATERIAL AND YEILD
                $id = $collection->id;
                $companyId = self::_getCompanyId();
                $output = $this->BaseModel->updateMadeByMaterial($id, $companyId);
                //dd($collection);
                ## ADD Lot Quantity in material balance                
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.materials-out.index');
                $this->JsonData['msg'] = $this->ModuleTitle.' created successfully.'; 
            }

        }
        catch(\Exception $e) {
            $this->JsonData['error_msg'] = $e->getMessage();
            $this->JsonData['msg'] = __('admin.ERR_SOMETHING_WRONG');
        }

        return response()->json($this->JsonData);
    }

   /* public function show($encID)
    {
        ## DEFAULT SITE SETTINGS
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
        $objPlan = new StoreProductionModel;
        $plans = $objPlan->getProductionPlans($companyId);
        $this->ViewData['plans']   = $plans;

        $id = base64_decode(base64_decode($encID));        
        $data = $this->BaseModel
        ->with([
            'assignedPlan' => function($q)
            {  
                $q->with(['assignedBatch'=> function($q){
                    $q->with('assignedProduct');
                }]);                
            }
        ])->where('company_id', $companyId)
        ->find($id);
        
        // END ASHVINI
        if(empty($data)) {            
            return redirect()->route('admin.materials-out.index');
        }
        ## ALL DATA        
        $this->ViewData['material'] = $data;
          
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreOutMaterialRequest $request, $encID)
    {
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update record, Something went wrong on server.';        
        $id = base64_decode(base64_decode($encID));
        //dd($id);
        try {
            $collection = $this->BaseModel->find($id);                 
            $collection = self::_storeOrUpdate($collection,$request);
            if($collection){
                ## CALCULATE LOSS MATERIAL AND YEILD
                $companyId = self::_getCompanyId();
                $output = $this->BaseModel->updateMadeByMaterial($id, $companyId); 
                //dd($output);
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.materials-out.index');
                $this->JsonData['msg'] = $this->ModuleTitle.' Updated successfully.'; 
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
        $collection->company_id     = self::_getCompanyId();
        $collection->user_id        = auth()->user()->id;
        $collection->plan_id        = $request->plan_id;
        $collection->sellable_qty   = $request->sellable_qty;
        $collection->course_powder  = $request->course_powder;
        $collection->rejection       = $request->rejection;
        $collection->dust_product          = $request->dust_product;
        /*$collection->loss_material             = $request->loss_material;
        $collection->yield             = $request->yield;*/
        $collection->status         = 0;      
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
        $companyId = self::_getCompanyId();
        $outputDetails = $this->BaseModel->with([
            'hasStock',
            'assignedPlan' => function($q)
            {  
                $q->with(['hasProductionMaterials' => function($q){
                    $q->with('mateialName');
                    $q->with('hasLot');    
                }]);
                $q->with(['assignedBatch'=> function($q){
                    $q->with('assignedProduct');
                }]);
                $q->with(['hasReturnMaterial' => function($q){
                    $q->with('hasReturnedMaterials');
                }]);               
            }
        ])->where('company_id', $companyId)
        ->find($id);
        $this->ViewData['object'] = $outputDetails;
        //dd($outputDetails);
        /*$this->ViewData['object'] = $this->BaseModel
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
        ->find($id);*/         
        //->find($id)->toArray(); //
        //dd($this->ViewData['object']);
        return view($this->ModuleView.'view', $this->ViewData);
    }
    public function showBatchViewReport($encId)
    {
       $id = base64_decode(base64_decode($encId));
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;
        $companyId = self::_getCompanyId();
        $outputDetails = $this->BaseModel->with([
            'hasStock',
            'assignedPlan' => function($q)
            {  
                $q->with(['hasProductionMaterials' => function($q){
                    $q->with('mateialName');
                    $q->with('hasLot');    
                }]);
                $q->with(['assignedBatch'=> function($q){
                    $q->with('assignedProduct');
                }]);
                $q->with(['hasReturnMaterial' => function($q){
                    $q->with('hasReturnedMaterials');
                }]);               
            }
        ])->where('company_id', $companyId)
        ->find($id);
        $this->ViewData['object'] = $outputDetails;
        return view($this->ModuleView.'showBatchReport', $this->ViewData);
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
            0 => 'store_out_materials.id',
            1 => 'store_out_materials.id',
            2 => 'store_batch_cards.batch_card_no',
            3 => 'products.code',            
            4 => 'store_out_materials.sellable_qty',
            5 => 'store_out_materials.loss_material',
            6 => 'store_out_materials.yield',                    
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY         
        $companyId = self::_getCompanyId();
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_out_materials.id, store_out_materials.plan_id, store_out_materials.sellable_qty, store_out_materials.loss_material, store_out_materials.yield, store_productions.batch_id, store_batch_cards.batch_card_no, products.name, products.code')
        ->leftjoin('store_productions', 'store_productions.id' , '=', 'store_out_materials.plan_id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_productions.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
        ->where('store_out_materials.company_id', $companyId)
        ->where('store_productions.deleted_at', null);
        //dd($modelQuery->toSql());
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
                ->where('store_productions.batch_id', $key);

            }
            if (!empty($request->custom['product_code'])) 
            {
                $custom_search = true;
                $key = $request->custom['product_code'];               
                $modelQuery = $modelQuery
                ->where('store_batch_cards.product_code',  $key);               
            }
            if (!empty($request->custom['sellable_qty'])) 
            {
                $custom_search = true;
                $key = $request->custom['sellable_qty'];               
                $modelQuery = $modelQuery
                ->where('store_out_materials.sellable_qty',  'LIKE', '%'.$key.'%');               
            }
            if (!empty($request->custom['loss_material'])) 
            {
                $custom_search = true;
                $key = $request->custom['loss_material'];               
                $modelQuery = $modelQuery
                ->where('store_out_materials.loss_material',  'LIKE', '%'.$key.'%');               
            } 
            if (!empty($request->custom['yield'])) 
            {
                $custom_search = true;
                $key = $request->custom['yield'];               
                $modelQuery = $modelQuery
                ->where('store_out_materials.yield',  'LIKE', '%'.$key.'%');               
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
                    $query->orwhere('store_out_materials.sellable_qty', 'LIKE', '%'.$search.'%');
                    $query->orwhere('store_out_materials.loss_material', 'LIKE', '%'.$search.'%');
                    $query->orwhere('store_out_materials.yield', 'LIKE', '%'.$search.'%');
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_out_materials.id', 'DESC');
                        
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get();  


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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_in_materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['batch_id']  = $row->batch_card_no;
                $data[$key]['product_code']  =  $row->name;
                $data[$key]['sellable_qty']  =  $row->sellable_qty;
                $data[$key]['loss_material']  =  number_format($row->loss_material, 2, '.', '');
                $data[$key]['yield']  =  number_format($row->yield, 2, '.', '');          

                if($row->status==1){
                    $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                }elseif($row->status==0) {
                 $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Closed</span>';
                }
                
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';

                $view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'" title="View"><span class="glyphicon glyphicon-eye-open"></a>';

                $data[$key]['actions'] =  '<div class="text-center">'.$view.'</div>';
                if(auth()->user()->can('store-material-output-add'))
                {
                    $data[$key]['actions'] =  '<div class="text-center">'.$view.' '.$edit.'</div>';
                }

        }
    }

    $objStore = new StoreBatchCardModel;
    $batchNos = $objStore->getBatchNumbers();
    $batch_no_string = '<select name="batch_id" id="batch-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_id'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';
    $objProduct = new ProductsModel;
    $products = $objProduct->getProducts($companyId);
    $product_code_string = '<select name="product_code" id="product-code" class="form-control my-select"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product_code'] == $product['id'] ? 'selected' : '').' >'.$product['code'].' ('.$product['name'].' )</option>';
        }
    $product_code_string .='</select>';
    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    $searchHTML['batch_id']     =  $batch_no_string;
    $searchHTML['product_code']     =  $product_code_string;
    $searchHTML['sellable_qty']   =  '<input type="text" class="form-control" id="sellable-qty" value="'.($request->custom['sellable_qty']).'" placeholder="Search...">';
    $searchHTML['loss_material']   =  '<input type="text" class="form-control" id="loss-material" value="'.($request->custom['loss_material']).'" placeholder="Search...">';
    $searchHTML['yield']   =  '<input type="text" class="form-control" id="yield" value="'.($request->custom['yield']).'" placeholder="Search...">';
    //$searchHTML['status']   =  '';  
    /*$searchHTML['status']   =  '<select name="status" id="search-status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Status</option>
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').' >Active</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').'>Closed</option>            
            </select>';*/
    /*$seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';removeSearch*/

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
public function getExistingPlan(Request $request)
{
    $this->JsonData['status'] = 'error';
    $this->JsonData['msg'] = 'Failed to get material Lots, Something went wrong on server.';
    try 
    {
        $plan_id   = $request->plan_id;
        $collection = $this->BaseModel->where('plan_id',$plan_id)->first();        
        $url = '';
        if($collection){               
            $url = route($this->ModulePath.'edit', [ base64_encode(base64_encode($collection->id))]);    
        }
        $this->JsonData['url']  = $url;        
        $this->JsonData['msg']  = 'Raw Materials';
        $this->JsonData['status']  = 'Success';

    } catch (Exception $e) 
    {
        $this->JsonData['exception'] = $e->getMessage();
    }

    return response()->json($this->JsonData);   
}

public function bulkDelete(Request $request)
{
    //dd($request->all());
    $this->JsonData['status'] = 'error';
    $this->JsonData['msg'] = 'Failed to delete records, Something went wrong on server.';

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

public function sendToSale(Request $request)
    {        
        DB::beginTransaction();
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update record, Something went wrong on server.'; 
        //dd($request->   all());
        try
        {
            $id = $request->id;
            $batchId = $request->batch_id;
            $productId = $request->product_id;
            $quantity = $request->quantity;
            $cost = $request->cost;
            ## MARK OUTPUT MATERIAL STATUS TO 1 THAT MEANS REVIEWED            
            $collection = $this->BaseModel->find($id);
            $collection->status   = 1;            
            ## SAVE DATA
            $collection->save();
            if($collection){
                $all_transactions = [];
                if($batchId > 0 && $productId > 0 && $quantity > 0 && $cost > 0) {
                    ## ADD THE ENTRY IN SALES STOCK 
                    $data['company_id'] = self::_getCompanyId();
                    $data['user_id'] = auth()->user()->id;
                    $data['material_out_id'] = $id;
                    $data['product_id'] = $productId;
                    $data['batch_id'] = $batchId;
                    $data['manufacturing_cost'] = $cost;
                    $data['quantity'] = $quantity;
                    $data['balance_quantity'] = $quantity;
                    $objStock = new StoreSaleStockModel;

                    if($objStock->addSalesStock($data)){
                        ## MARK BATCH AS CLOSED
                        $objBatch = new StoreBatchCardModel;
                        $batchCollection = $objBatch->find($batchId);
                        $batchCollection->review_status = "closed";                       
                        if($batchCollection->save()){
                            $all_transactions[] = 1;
                        } else {
                            $all_transactions[] = 0;
                        }
                            
                    } else {                       
                        $all_transactions[] = 0;
                        $this->JsonData['msg'] = 'This Batch is already sent to Sale Stock.'; 
                    }
                    
                } else {
                     $all_transactions[] = 0;
                }
                if (!in_array(0,$all_transactions)) 
                {
                    $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                    //$this->JsonData['url'] = route($this->ModulePath.'index');
                    $this->JsonData['msg'] = 'Batch is sent to Sale Stock successfully.';
                    DB::commit();
                }               
            } else {
                DB::rollback();
            }           
        }
        catch(\Exception $e) {
            $this->JsonData['msg'] = $e->getMessage();
            DB::rollback();
        }

        return response()->json($this->JsonData);
        //dd($request->all());
    }
/*public function sendToBilling(Request $request, $encId)
    {        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update record, Something went wrong on server.';       
        $id = base64_decode(base64_decode($encId));        
        try
        {
            $batchId = $request->batch_id;
            ## MARK OUTPUT MATERIAL STATUS TO 1 THAT MEANS REVIEWED
            $collection = $this->BaseModel->find($id);
            $collection->status   = !empty($request->status) ? 1 : 0;            
            ## SAVE DATA
            $collection->save();
            if($collection){
                $msg = "Batch is saved successfully.";
                ## MARK BATCH AS CLOSED
                if($batchId > 0){
                   $objBatch = new StoreBatchCardModel;
                    $batchCollection = $objBatch->find($batchId);
                    $batchCollection->review_status = !empty($request->review_status) ? "closed" : "open";
                    $batchCollection->save();                    
                }
                
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = url('admin/materials-out');
                $this->JsonData['msg'] = $msg; 
            }           
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
        //dd($request->all());
    }*/

}
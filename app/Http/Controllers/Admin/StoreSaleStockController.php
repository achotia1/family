<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreSaleStockModel;
use App\Models\StoreStockCorrectionModel;
use App\Models\StoreBatchCardModel;
use App\Models\ProductsModel;
use App\Models\StoreSaleInvoiceHasProductsModel;


use App\Http\Requests\Admin\StoreCorrectStockRequest;
use App\Http\Requests\Admin\StoreOpeningStockRequest;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use DB;
class StoreSaleStockController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(
        StoreSaleStockModel $StoreSaleStockModel
       
    )
    {
        $this->BaseModel  = $StoreSaleStockModel;
        
        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Sales Stock';
        $this->ModuleView  = 'admin.store-in-stock.';
        $this->ModulePath = 'admin.sale-stock.';

        ## PERMISSION MIDDELWARE
        //$this->middleware(['permission:store-material-in-listing'], ['only' => ['getRecords']]);
        //$this->middleware(['permission:store-material-in-add'], ['only' => ['edit','update','create','store','bulkDelete']]);
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
        $this->ViewData['moduleTitle']  = 'Add Opening '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['moduleAction'] = 'Add Opening '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        $companyId = self::_getCompanyId();

        $objProduct = new ProductsModel;
        $products = $objProduct->getProducts($companyId);
        
        //$this->ViewData['batchNo']   = $batchNo;
        $this->ViewData['products']   = $products;
        
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }
    public function store(StoreOpeningStockRequest $request)
    {        
        DB::beginTransaction();
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Batch, Something went wrong on server.';
        try {

            $collection = new StoreBatchCardModel;
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $all_transactions = [];
                $collStock = $this->BaseModel;
                $collStock->company_id = self::_getCompanyId();
                $collStock->user_id   = auth()->user()->id;
                $collStock->product_id   = $collection->product_code;
                $collStock->batch_id   = $collection->id;
                $collStock->manufacturing_cost   = $request->manufacturing_cost;
                $collStock->quantity   = $request->quantity;
                $collStock->balance_quantity   = $request->quantity;
                $collStock->status = 0;
                
                ## SAVE STOCK DATA DATA
                if($collStock->save()){
                    $all_transactions[] = 1;
                } else {
                    $all_transactions[] = 0;
                }
                if (!in_array(0,$all_transactions)) 
                {
                    $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                    $this->JsonData['url'] = route('admin.sale-stock.index');
                    $this->JsonData['msg'] = 'Opening Stock is created successfully.'; 
                    DB::commit();
                }                
            } else {
                DB::rollback();
            }

        }
        catch(\Exception $e) {
            $this->JsonData['error_msg'] = $e->getMessage();
            $this->JsonData['msg'] = __('admin.ERR_SOMETHING_WRONG');
            DB::rollback();
        }

        return response()->json($this->JsonData);
    }

    public function _storeOrUpdate($collection, $request)
    {
        $collection->company_id        = self::_getCompanyId();
        $collection->user_id        = auth()->user()->id;
        $collection->product_code        = $request->product_code;
        $collection->batch_card_no   = $request->batch_card_no;
        $collection->batch_qty             = $request->quantity;
        $collection->status             = 0;
        $collection->review_status      = 'closed';
        
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
        $outputDetails = $this->BaseModel->with(['assignedProduct','assignedBatch'])->where('company_id', $companyId)
        ->find($id);
        $this->ViewData['object'] = $outputDetails;
        //dd($outputDetails);        
        return view($this->ModuleView.'view', $this->ViewData);
    }
    public function destroy($encID)
    {        
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to delete user, Something went wrong on server.'   ;
        $id = base64_decode(base64_decode($encID));
        $objInvoice = new StoreSaleInvoiceHasProductsModel;
        $availableCount = $objInvoice->where('sale_stock_id',$id)->count();
        if($availableCount>0) 
        {
            $this->JsonData['status'] = __('admin.RESP_ERROR');
            $this->JsonData['msg'] = 'Cant delete this stock recod which has beed used in sales invoce module.'; 
            return response()->json($this->JsonData);
            exit();
        }
        $baseModel = $this->BaseModel->find($id);
        try {
            if($baseModel->delete())
            {
                $this->JsonData['status'] = 'success';
                $this->JsonData['msg'] = $this->ModuleTitle.' deleted successfully.';
            }
        }
        catch (\Exception $e){
            $this->JsonData['error_msg'] = $e->getMessage();
        }
        return response()->json($this->JsonData);
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
            0 => 'store_sales_stock.id',
            1 => 'store_batch_cards.batch_card_no',            
            2 => 'products.code',            
            3 => 'store_sales_stock.quantity',
            4 => 'store_sales_stock.balance_quantity',
            5 => 'store_sales_stock.manufacturing_cost', 
            6 => 'store_sales_stock.id'
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY         
        $companyId = self::_getCompanyId();
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_sales_stock.id, store_sales_stock.manufacturing_cost, store_sales_stock.quantity, store_sales_stock.balance_quantity, store_sales_stock.status, store_batch_cards.batch_card_no, products.name, products.code')       
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_sales_stock.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_sales_stock.product_id')
        ->where('store_sales_stock.company_id', $companyId);
        
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['batch_code'])) 
            {
                $custom_search = true;
                $key = $request->custom['batch_code'];                
                $modelQuery = $modelQuery
                ->where('store_sales_stock.batch_id', '=', $key);

            }
            if (!empty($request->custom['product'])) 
            {
                $custom_search = true;
                $key = $request->custom['product'];               
                $modelQuery = $modelQuery
                ->where('store_sales_stock.product_id', '=', $key);               
            }

            if (!empty($request->custom['quantity'])) 
            {
                $custom_search = true;
                $key = $request->custom['quantity'];               
                $modelQuery = $modelQuery
                ->where('store_sales_stock.quantity', '>', $key);

            }            
            if (isset($request->custom['balance_quantity'])) 
            {
                $custom_search = true;
                $key = $request->custom['balance_quantity'];
                $modelQuery = $modelQuery
                ->where('store_sales_stock.balance_quantity', '>', $key);
            }            
            if (isset($request->custom['manufacturing_cost'])) 
            {
                $custom_search = true;
                $key = $request->custom['manufacturing_cost'];
                $modelQuery = $modelQuery
                ->where('store_sales_stock.manufacturing_cost', '>', $key);
            }
            if (isset($request->custom['status'])) 
            {
                $custom_search = true;
                $key = $request->custom['status'];
                $modelQuery = $modelQuery
                ->where('store_sales_stock.status', $key);
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
                    $query->orwhere('store_sales_stock.quantity', 'LIKE', '%'.$search.'%');
                    $query->orwhere('store_sales_stock.balance_quantity', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_sales_stock.manufacturing_cost', 'LIKE', '%'.$search.'%');   
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_sales_stock.id', 'DESC');
                        
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

                //$data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_in_materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['batch_code']  = $row->batch_card_no;
                $data[$key]['product']  =  $row->code." ( ".$row->name." )";
                $data[$key]['quantity']  =  number_format($row->quantity, 2, '.', '');
                $data[$key]['balance_quantity']  =  number_format($row->balance_quantity, 2, '.', '');
                $data[$key]['manufacturing_cost']  =  number_format($row->manufacturing_cost, 2, '.', '');          
                if($row->status==1){
                    $data[$key]['status'] = 'No';
                }elseif($row->status==0) {
                 $data[$key]['status'] = 'Yes';
                }
                $view = '';
                $delete = '<a href="javascript:void(0)" onclick="return deleteCollection(this)" data-href="'.route($this->ModulePath.'destroy', [base64_encode(base64_encode($row->id))]) .'" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>';               
                $correctBalIcon = '<a href="'.route($this->ModulePath.'correct-balance',[ base64_encode(base64_encode($row->id))]).'" title="Correct Balance"><span class="glyphicon glyphicon-ok-circle"></a>';
                $view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'" title="View"><span class="glyphicon glyphicon-eye-open"></a>';
                $data[$key]['actions'] = '';
                /*if(auth()->user()->can('store-material-in-add'))
                {*/
                    $data[$key]['actions'] =  '<div class="text-center">'.$view.' '.$correctBalIcon.' '.$delete.'</div>';
                /*}*/

        }
    }

    $objStore = new StoreBatchCardModel;
    $batchNos = $objStore->getClosedBatches($companyId);
    $batch_no_string = '<select name="batch_code" id="batch-code" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_code'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';
    $objProduct = new ProductsModel;
    $products = $objProduct->getProducts($companyId);
    $product_code_string = '<select name="product" id="product" class="form-control my-select"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product'] == $product['id'] ? 'selected' : '').' >'.$product['code'].' ('.$product['name'].' )</option>';
        }
    $product_code_string .='</select>';
    $material_id_string ='';
    ## SEARCH HTML
    $searchHTML['id']       =  '';
    //$searchHTML['select']   =  '';
    $searchHTML['batch_code']     =  $batch_no_string;
    $searchHTML['product']     =  $product_code_string;
    $searchHTML['quantity']   =  '<input type="text" class="form-control" id="quantity" value="'.($request->custom['quantity']).'" placeholder="More than...">';
    $searchHTML['balance_quantity']   =  '<input type="text" class="form-control" id="balance-quantity" value="'.($request->custom['balance_quantity']).'" placeholder="More than...">';
    $searchHTML['manufacturing_cost']   =  '<input type="text" class="form-control" id="manufacturing-cost" value="'.($request->custom['manufacturing_cost']).'" placeholder="More than...">';

    $searchHTML['status']   =  '<select name="status" id="status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Select</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').' >Yes</option>
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').'>No</option>            
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
    /*public function bulkDelete(Request $request)
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

            $store_lot_count = $this->ProductionHasMaterialModel->whereIn('lot_id',$arrID)->count();
            if($store_lot_count>0) 
            {
                $this->JsonData['status'] = __('admin.RESP_ERROR');
                $this->JsonData['msg'] = 'Cant delete this Lot which is assigned in Production Module'; 
                return response()->json($this->JsonData);
                exit();
            }

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
    }*/
    public function correctBalance($encID)
    {       
        $id = base64_decode(base64_decode($encID));
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Correct Stock Balance';
        $this->ViewData['moduleAction'] = 'Correct Stock Balance';
        $this->ViewData['moduleTitleInfo'] = "Stock Balance Information";
        $this->ViewData['modulePath']   = $this->ModulePath;        
        $companyId = self::_getCompanyId();
        $data = $this->BaseModel->with(['assignedProduct','assignedBatch'])->where('company_id', $companyId)
        ->find($id);
        //dd($data);
        if(empty($data)) {            
            return redirect()->route('admin.sale-stock.index');
        } 
        $this->ViewData['stock'] = $data;       
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'correct', $this->ViewData);       
    }
    public function updateBalance(StoreCorrectStockRequest $request)
    {
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update record, Something went wrong on server.';
        try 
        {           
            $id = $request->id;
            $collection = $this->BaseModel->find($id);
            $collection->balance_quantity = $request->corrected_balance;
            $collection->balance_corrected_at = Carbon::today();
            $collection->save();           
            $correctObj = new StoreStockCorrectionModel;
            $correctObj->user_id = auth()->user()->id;
            $correctObj->stock_id = $id;
            $correctObj->previous_balance = $request->previous_balance;
            $correctObj->corrected_balance = $request->corrected_balance;
            if($correctObj->save()){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.sale-stock.index');
                $this->JsonData['msg'] = 'Stock Balance updated successfully.';
            }
        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);        
    }
    /*public function create()
    {}

    public function store(StoreInMaterialRequest $request)
    { }*/

   /* public function show($encID)
    {}*/
}
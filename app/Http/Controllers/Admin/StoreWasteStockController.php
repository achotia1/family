<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreWasteStockModel;
use App\Models\StoreWastageCorrectionModel;
use App\Models\StoreBatchCardModel;
use App\Models\ProductsModel;

use App\Http\Requests\Admin\StoreCorrectWastageRequest;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
//use DB;
class StoreWasteStockController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreWasteStockModel $StoreWasteStockModel
    )
    {
        $this->BaseModel  = $StoreWasteStockModel;
        

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Wastage Material';
        $this->ModuleView  = 'admin.store-waste-stock.';
        $this->ModulePath = 'admin.wastage-material.';

        ## PERMISSION MIDDELWARE
        /*$this->middleware(['permission:store-material-plan-listing'], ['only' => ['getRecords']]);
        $this->middleware(['permission:store-material-plan-add'], ['only' => ['edit','update','create','store','destroy']]);*/
    }
    

    public function index()
    {
        $companyId = self::_getCompanyId();
        $rcester_companyId = config('constants.RCESTERCOMPANY');
        $showWastage = true;
        if($companyId==$rcester_companyId){
            $showWastage = false;
            return redirect()->route('admin.dashboard');
        }
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;        

        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'index', $this->ViewData);
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
            0 => 'store_waste_stock.id',
            1 => 'store_batch_cards.batch_card_no',
            2 => 'products.code',
            3 => 'store_waste_stock.balance_course', 
            4 => 'store_waste_stock.balance_rejection',
            5 => 'store_waste_stock.balance_dust',
            6 => 'store_waste_stock.balance_loose',                   
        );       

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/
        $companyId = self::_getCompanyId();
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_waste_stock.id, store_waste_stock.balance_course, store_waste_stock.balance_rejection, store_waste_stock.balance_dust, store_waste_stock.balance_loose, store_batch_cards.batch_card_no,  products.name, products.code')        
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_waste_stock.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
        ->where('store_waste_stock.company_id', $companyId);
        //dd($modelQuery->toSql());
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();
        
        //dd($request->custom['balance_course']);
        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['batch_id'])) 
            {
                $custom_search = true;
                $key = $request->custom['batch_id'];                
                $modelQuery = $modelQuery
                ->where('store_waste_stock.batch_id', $key);
            }

            if (!empty($request->custom['product_code'])) 
            {
                $custom_search = true;
                $key = $request->custom['product_code'];               
                $modelQuery = $modelQuery
                ->where('store_waste_stock.product_id',  $key);               
            }
            if (($request->custom['balance_course'] != null) && $request->custom['balance_course'] >= 0) 
            {
                $custom_search = true;
                $key = $request->custom['balance_course'];                
                $modelQuery = $modelQuery
                ->where('store_waste_stock.balance_course', '>', $key);                
            }            
            if (($request->custom['balance_rejection'] != null) && $request->custom['balance_rejection'] >= 0) 
            {
                $custom_search = true;
                $key = $request->custom['balance_rejection'];                
                $modelQuery = $modelQuery
                ->where('store_waste_stock.balance_rejection', '>', $key);                
            }
            if (($request->custom['balance_dust'] != null) && $request->custom['balance_dust'] >= 0) 
            {
                $custom_search = true;
                $key = $request->custom['balance_dust'];                
                $modelQuery = $modelQuery
                ->where('store_waste_stock.balance_dust', '>', $key);                
            }
            if (($request->custom['balance_loose'] != null) && $request->custom['balance_loose'] >= 0) 
            {
                $custom_search = true;
                $key = $request->custom['balance_loose'];                
                $modelQuery = $modelQuery
                ->where('store_waste_stock.balance_loose', '>', $key);                
            }
        }
        //dd($modelQuery->toSql());
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
            $modelQuery = $modelQuery->orderBy('store_waste_stock.id', 'DESC'); 
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_productions[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['batch_id']  = $row->batch_card_no;
                $data[$key]['product_code']  =  $row->code." ( ".$row->name." )";
                $data[$key]['balance_course']  =  number_format($row->balance_course, 2, '.','');
                $data[$key]['balance_rejection']  =  number_format($row->balance_rejection, 2, '.','');
                $data[$key]['balance_dust']  =  number_format($row->balance_dust, 2, '.','');
                $data[$key]['balance_loose']  =  number_format($row->balance_loose, 2, '.','');              
                $edit = $delete = $view = '';               
                $view = '<a href="'.route($this->ModulePath.'correct-balance',[ base64_encode(base64_encode($row->id))]).'" title="Correct Balance"><span class="glyphicon glyphicon-ok-circle"></a>';
                
                $data[$key]['actions'] = '';
                $data[$key]['actions'] =  '<div class="text-center">'.$view.'</div>';
                /*if(auth()->user()->can('store-material-plan-add'))
                {*/
                    $data[$key]['actions'] =  '<div class="text-center">'.$view.' '.$edit.' '.$delete.'</div>';
                /*}*/

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
    
    $searchHTML['balance_course']     =  '<input type="text" class="form-control" id="balance-course" value="'.($request->custom['balance_course']).'" placeholder="More than...">';
      
    $searchHTML['balance_rejection']     =  '<input type="text" class="form-control" id="balance-rejection" value="'.($request->custom['balance_rejection']).'" placeholder="More than...">';

    $searchHTML['balance_dust']     =  '<input type="text" class="form-control" id="balance-dust" value="'.($request->custom['balance_dust']).'" placeholder="More than...">';

    $searchHTML['balance_loose']     =  '<input type="text" class="form-control" id="balance-loose" value="'.($request->custom['balance_loose']).'" placeholder="More than...">';

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

public function correctBalance($encID)
{       
        $id = base64_decode(base64_decode($encID));
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Correct Balance';
        $this->ViewData['moduleAction'] = 'Correct Balance';
        $this->ViewData['moduleTitleInfo'] = "Balance Information";
        $this->ViewData['modulePath']   = $this->ModulePath;        
        $companyId = self::_getCompanyId();

        $rcester_companyId = config('constants.RCESTERCOMPANY');
        $showWastage = true;
        if($companyId==$rcester_companyId){
            $showWastage = false;
            return redirect()->route('admin.dashboard');
        }
        
        $data = $this->BaseModel->with([
            'assignedBatch', 'assignedProduct'           
        ])->where('company_id', $companyId)
        ->find($id);
        //dd($data);
        if(empty($data)) {            
            return redirect()->route('admin.wastage-material.index');
        } 
        $this->ViewData['material'] = $data;       
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'correct', $this->ViewData);
       
}
public function updateBalance(StoreCorrectWastageRequest $request)
    {
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update record, Something went wrong on server.';
        try 
        {           
            $id = $request->id;
            $collection = $this->BaseModel->find($id);
            $collection->balance_course = $request->corrected_cbalance;
            $collection->balance_rejection = $request->corrected_rbalance;
            $collection->balance_dust = $request->corrected_dbalance;
            $collection->balance_loose = $request->corrected_lbalance;
            $collection->balance_corrected_at = Carbon::today();
            $collection->save();
            
            $correctObj = new StoreWastageCorrectionModel;
            $correctObj->user_id = auth()->user()->id;
            $correctObj->wastage_id = $id;
            $correctObj->previous_cbalance = $request->previous_cbalance;
            $correctObj->corrected_cbalance = $request->corrected_cbalance;
            $correctObj->previous_rbalance = $request->previous_rbalance;
            $correctObj->corrected_rbalance = $request->corrected_rbalance;
            $correctObj->previous_dbalance = $request->previous_dbalance;
            $correctObj->corrected_dbalance = $request->corrected_dbalance;
            $correctObj->previous_lbalance = $request->previous_lbalance;
            $correctObj->corrected_lbalance = $request->corrected_lbalance;
            //dd($correctObj);
            if($correctObj->save()){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.wastage-material.index');
                $this->JsonData['msg'] = 'Wastage Balance updated successfully.';
            }
        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);        
    }    

}
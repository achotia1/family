<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


## MODELS

use App\Models\StoreWasteStockModel;
use App\Models\StoreProductionModel;
use App\Models\StoreBatchCardModel;
use App\Models\StoreRawMaterialModel;
use App\Models\StoreInMaterialModel;
use App\Models\ProductionHasMaterialModel;
use App\Models\ProductsModel;
use App\Models\StoreOutMaterialModel;
use App\Models\StoreReturnedMaterialModel;

use App\Http\Requests\Admin\StoreProductionRequest;
use App\Traits\GeneralTrait;

use DB;
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
            2 => 'store_waste_stock.id',
            3 => 'store_waste_stock.id', 
            4 => 'store_waste_stock.id',                   
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
                ->where('store_waste_stock.batch_id', $key);
            }

            if (!empty($request->custom['product_code'])) 
            {
                $custom_search = true;
                $key = $request->custom['product_code'];               
                $modelQuery = $modelQuery
                ->where('store_waste_stock.product_id',  $key);               
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
     
    $batch_no_string = '<select name="batch_no" id="batch-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
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

    

}
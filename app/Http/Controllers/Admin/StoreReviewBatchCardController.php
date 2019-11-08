<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


## MODELS
use App\Models\StoreBatchCardModel;
use App\Models\ProductsModel;
use App\Models\StoreProductionModel;
use App\Models\StoreReturnedMaterialModel;

use App\Http\Requests\Admin\StoreReviewBatchCardRequest;
use App\Traits\GeneralTrait;

use Illuminate\Support\Facades\Validator;
class StoreReviewBatchCardController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreBatchCardModel $StoreBatchCardModel
    )
    {
        $this->BaseModel  = $StoreBatchCardModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Batch';
        $this->ModuleView  = 'admin.store-review-batch.';
        $this->ModulePath = 'admin.review-batch-card.';

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
        $this->ViewData['pendingBatches']   = $this->BaseModel->where('status', 1)->where('is_reviewed', 'no')->orderBy('id', 'DESC')->with(['assignedProduct'])->get();
        //dd('sfdsf');
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'index', $this->ViewData);
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
            'assignedProduct'
        ])         
        ->find($id);        

        $objStore = new StoreProductionModel;
        $associatedMaterial = $objStore->where('batch_no', $id)->where('status', 1)->with(['associatedMateials'])->get();

        $this->ViewData['materials'] = $associatedMaterial;

        $objReturn = new StoreReturnedMaterialModel;
        $this->ViewData['returnedData'] = $objReturn->getBatchReturnMaterial($id);  
        //dd($this->ViewData['returnedData']);     
        return view($this->ModuleView.'view', $this->ViewData); 
    }
    public function sendToBilling(StoreReviewBatchCardRequest $request, $encId)
    {        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update record, Something went wrong on server.';       
        $id = base64_decode(base64_decode($encId));
        try
        {
            $collection = $this->BaseModel->find($id);
            $collection->sell_cost   = $request->sell_cost;           
            $collection->is_reviewed   = $request->is_reviewed;            

            ## SAVE DATA
            $collection->save();
            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = url('admin/review-batch-card');
                $this->JsonData['msg'] = 'Batch is Reviewed successfully.'; 
            }           
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
        //dd($request->all());
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
            0 => 'store_batch_cards.id',
            1 => 'store_batch_cards.id',
            2 => 'store_batch_cards.product_code',
            3 => 'store_batch_cards.batch_card_no',
            4 => 'store_batch_cards.batch_qty',
            5 => 'store_batch_cards.status',            
        );

        /*--------------------------------------
        |   MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY       
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_batch_cards.id, store_batch_cards.product_code, store_batch_cards.batch_card_no, store_batch_cards.batch_qty,store_batch_cards.status, store_batch_cards.is_reviewed, products.name, products.code')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
        ->where('store_batch_cards.status', 1)->where('is_reviewed', 'no');        

        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['product_code'])) 
            {
                $custom_search = true;

                $key = $request->custom['product_code'];
                //dd($key);
                $modelQuery = $modelQuery
                ->where('store_batch_cards.product_code',  $key);

            }

            if (!empty($request->custom['batch_card_no'])) 
            {
                $custom_search = true;

                $key = $request->custom['batch_card_no'];
                //dd($key);
                $modelQuery = $modelQuery
                ->where('store_batch_cards.batch_card_no', 'LIKE', '%'.$key.'%');

            }
        }

        if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];

                 $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('products.name', 'LIKE', '%'.$search.'%');
                    $query->orwhere('products.code', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_batch_cards.batch_qty', 'LIKE', '%'.$search.'%');   
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_batch_cards.is_reviewed', 'ASC'); 
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get(['store_batch_cards.id', 
            'store_batch_cards.product_code', 
            'store_batch_cards.batch_card_no', 
            'store_batch_cards.batch_qty',
            'store_batch_cards.status',
            'store_batch_cards.is_reviewed', 
            'products.name',
            'products.code',          
        ]);  


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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_batch_cards[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['product_code']  = $row->code." ( ".$row->name." )";

                $data[$key]['batch_card_no']  =  $row->batch_card_no;
                $data[$key]['batch_qty']  =  $row->batch_qty;
                
                if($row->is_reviewed=='no'){
                    $data[$key]['is_reviewed'] = '<span class="theme-green semibold text-center f-18">Pending</span>';
                }elseif($row->is_reviewed=='yes') {
                 $data[$key]['is_reviewed'] = '<span class="theme-gray semibold text-center f-18">Reviewed</span>';
                }                
                // $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';
                $view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'"><img src="'.url('/assets/admin/images').'/icons/eye.svg" alt=" view"></a>';
                $data[$key]['actions'] = '';

               /* if(auth()->user()->can('batch-add'))
                {*/
                    $data[$key]['actions'] =  '<div class="text-center">'.$view.'</div>';
               /* }*/

         }
     }
    $objProduct = new ProductsModel;
    $products = $objProduct->getProducts();
    $product_code_string = '<select name="product_code" id="product-code" class="form-control my-select"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product_code'] == $product['id'] ? 'selected' : '').' >'.$product['code'].' ('.$product['name'].' )</option>';
        }
    $product_code_string .='</select>';
    $searchHTML['product_code'] = $product_code_string;

    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';    
    $searchHTML['batch_card_no']     =  '<input type="text" class="form-control" id="batch-card-no" value="'.($request->custom['batch_card_no']).'" placeholder="Search...">';
    $searchHTML['batch_qty']   =  '';     
    $searchHTML['is_reviewed']   =  '';

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
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


## MODELS
use App\Models\StoreBatchCardModel;
use App\Models\ProductsModel;

use App\Http\Requests\Admin\StoreBatchCardRequest;
use App\Traits\GeneralTrait;

class StoreBatchCardController extends Controller
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
        $this->ModuleView  = 'admin.store-batch-cards.';
        $this->ModulePath = 'admin.rms-store.';

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
        
        /*$pendingBatches = $this->BaseModel->where('status', 1)->where('is_reviewed', 'no')->orderBy('id', 'DESC')->with(['assignedProduct'])->get();

        foreach($pendingBatches as $key => $bat){
            echo "<br>".$bat->assignedProduct->name;
            echo "<br>".$bat->batch_card_no;
        }
        exit;*/
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
        $batchNo = $objStore->getBatchCardNo();

        $objProduct = new ProductsModel;
        $products = $objProduct->getProducts();
        
        $this->ViewData['batchNo']   = $batchNo;
        $this->ViewData['products']   = $products;
        
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreBatchCardRequest $request)
    {        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Batch, Something went wrong on server.';
        try {

            $collection = new $this->BaseModel;
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.rms-store.index');
                $this->JsonData['msg'] = $this->ModuleTitle.' created successfully.'; 
            }

        }
        catch(\Exception $e) {
            $this->JsonData['error_msg'] = $e->getMessage();
            $this->JsonData['msg'] = __('admin.ERR_SOMETHING_WRONG');
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

        $objProduct = new ProductsModel;
        $products = $objProduct->getProducts();        
        
        $this->ViewData['products']   = $products;

        $companyId = self::_getCompanyId();
        $data = $this->BaseModel->where('store_batch_cards.id', base64_decode(base64_decode($encID)))->where('store_batch_cards.company_id', $companyId)->first();
        if(empty($data)) {            
            return redirect()->route('admin.rms-store.index');
        }

        ## ALL DATA
        // $this->ViewData['branch'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));
        $this->ViewData['branch'] = $data;

        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreBatchCardRequest $request, $encID)    {
        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Branch, Something went wrong on server.';       

        $id = base64_decode(base64_decode($encID));
        try {

            $collection = $this->BaseModel->find($id);   
            
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.rms-store.index');
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
        $collection->company_id        = self::_getCompanyId();
        $collection->product_code        = $request->product_code;
        $collection->batch_card_no   = $request->batch_card_no;
        $collection->batch_qty             = $request->batch_qty;         
        $collection->status             = !empty($request->status) ? 1 : 0;
        
        ## SAVE DATA
        $collection->save();
        
        return $collection;
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
            2 => 'products.code',
            3 => 'store_batch_cards.batch_card_no',
            4 => 'store_batch_cards.batch_qty',
            5 => 'store_batch_cards.status',            
        );

        /*--------------------------------------
        |   MODEL QUERY AND FILTER
        ------------------------------*/
        $companyId = self::_getCompanyId();
        ## START MODEL QUERY       
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_batch_cards.id, store_batch_cards.product_code, store_batch_cards.batch_card_no, store_batch_cards.batch_qty,store_batch_cards.status, products.name, products.code')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
        ->where('store_batch_cards.company_id', $companyId);         

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
            if (isset($request->custom['status'])) 
            {
                $custom_search = true;
                $key = $request->custom['status'];
                $modelQuery = $modelQuery
                ->where('store_batch_cards.status', $key);
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
            $modelQuery = $modelQuery->orderBy('store_batch_cards.status', 'ASC'); 
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get();  
        /*['store_batch_cards.id', 
            'store_batch_cards.product_code', 
            'store_batch_cards.batch_card_no', 
            'store_batch_cards.batch_qty',
            'store_batch_cards.status', 
            'products.name',
            'products.code',          
        ]*/

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
                
                if($row->status==1){
                    $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                }elseif($row->status==0) {
                 $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Inactive</span>';
                }                
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';

                $data[$key]['actions'] = '';

               /* if(auth()->user()->can('batch-add'))
                {*/
                    $data[$key]['actions'] =  '<div class="text-center">'.$edit.'</div>';
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
    //$searchHTML['status']   =  '';

    $searchHTML['status']   =  '<select name="status" id="search-status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Status</option>
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').' >Active</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').'>Inactive</option>            
            </select>';

    /*$seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';*/
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

}
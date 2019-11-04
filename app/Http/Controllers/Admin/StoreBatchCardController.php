<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


// models
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

       /* $this->middleware(['permission:manage-batches'], ['only' => ['edit','update','getRecords','bulkDelete']]);
        $this->middleware(['permission:batch-add'], ['only' => ['create','store']]);*/
    }
    

    public function index()
    {
         // Default site settings
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;        

        // view file with data
        return view($this->ModuleView.'index', $this->ViewData);
    }

    public function create()
    {
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Add New '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['moduleAction'] = 'Add New '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        /* $todaysRecords = RmsStoreModel::whereDate('created_at', Carbon::today())->get()->count();  */      
        $objStore = new StoreBatchCardModel();
        $batchNo = $objStore->getBatchCardNo();

        $objProduct = new ProductsModel();
        $products = $objProduct->getProducts();
        
        $this->ViewData['batchNo']   = $batchNo;
        $this->ViewData['products']   = $products;
        //dd($this->ViewData['batchNo']);
        // view file with data
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
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['modulePath']   = $this->ModulePath;

        $objProduct = new ProductsModel();
        $products = $objProduct->getProducts();        
        
        $this->ViewData['products']   = $products;

        // All data
        $this->ViewData['branch'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));

        // view file with data
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
        //Save data
        $collection->save();
        
        return $collection;
    }

    public function getRecords(Request $request)
    {
		//dd($request->all());
        /*--------------------------------------
        |  Variables
        ------------------------------*/

        // skip and limit
        $start = $request->start;
        $length = $request->length;

            // serach value
        $search = $request->search['value']; 

            // order
        $column = $request->order[0]['column'];
        $dir = $request->order[0]['dir'];

            // filter columns
        $filter = array(
            0 => 'store_batch_cards.id',
            1 => 'store_batch_cards.id',
            2 => 'store_batch_cards.product_code',
            3 => 'store_batch_cards.batch_card_no',
            4 => 'store_batch_cards.batch_qty',
            5 => 'store_batch_cards.status',            
        );

        /*--------------------------------------
        |  Model query and filter
        ------------------------------*/

        // start model query        
        /*$modelQuery =  $this->BaseModel;*/
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_batch_cards.id, store_batch_cards.product_code, store_batch_cards.batch_card_no, store_batch_cards.batch_qty,store_batch_cards.status, products.name')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code');       

        // get total count 
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

            // filter options
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
                    $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_batch_cards.batch_qty', 'LIKE', '%'.$search.'%');   
                });              

            }
        }

            // get total filtered
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

            // offset and limit
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
        ->get(['store_batch_cards.id', 
            'store_batch_cards.product_code', 
            'store_batch_cards.batch_card_no', 
            'store_batch_cards.batch_qty',
            'store_batch_cards.status', 
            'products.name',          
        ]);  


        /*--------------------------------------
        |  data binding
        ------------------------------*/

        $data = [];

        if (!empty($object) && sizeof($object) > 0)
        {
            $count =1;
            foreach ($object as $key => $row)
            {

                $data[$key]['id'] = $row->id;

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_batch_cards[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['product_code']  = $row->name;

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
    $objProduct = new ProductsModel();
    $products = $objProduct->getProducts();
    $product_code_string = '<select name="product_code" id="product-code" class="form-control my-select"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product_code'] == $product['id'] ? 'selected' : '').' >'.$product['name'].'</option>';
        }
    $product_code_string .='</select>';
    $searchHTML['product_code'] = $product_code_string;

    // search html
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    //$searchHTML['product_code']     =  '<input type="text" class="form-control" id="product-code" value="'.($request->custom['product_code']).'" placeholder="Search...">';
    $searchHTML['batch_card_no']     =  '<input type="text" class="form-control" id="batch-card-no" value="'.($request->custom['batch_card_no']).'" placeholder="Search...">';
    $searchHTML['batch_qty']   =  '';     
    $searchHTML['status']   =  '';

    $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';    

    $searchHTML['actions'] = $seachAction;
    array_unshift($data, $searchHTML);

        // wrapping up
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
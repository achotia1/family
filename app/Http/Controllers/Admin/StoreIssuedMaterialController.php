<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreIssuedMaterialModel;
use App\Models\StoreRawMaterialModel;
use App\Models\StoreBatchCardModel;
use App\Models\StoreProductionModel;

use App\Http\Requests\Admin\StoreIssuedMaterialRequest;
use App\Traits\GeneralTrait;

class StoreIssuedMaterialController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreIssuedMaterialModel $StoreIssuedMaterialModel,
        StoreRawMaterialModel $StoreRawMaterialModel,
        StoreProductionModel $StoreProductionModel
    )
    {
        $this->BaseModel  = $StoreIssuedMaterialModel;
        $this->StoreRawMaterialModel  = $StoreRawMaterialModel;
        $this->StoreProductionModel  = $StoreProductionModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Issued Material';
        $this->ModuleView  = 'admin.store-issued-material.';
        $this->ModulePath = 'admin.sales.';

        ## PERMISSION MIDDELWARE
        /*$this->middleware(['permission:manage-materials'], ['only' => ['edit','update','create','store','getRecords','bulkDelete']]);*/
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
        
        $objStore = new StoreBatchCardModel;
        $batchNos = $objStore->getBatchNumbers();

        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getMaterialNumbers();
        $this->ViewData['materialIds']   = $materialIds;
        $this->ViewData['batchNos']   = $batchNos;
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreIssuedMaterialRequest $request)
    {        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Material, Something went wrong on server.'; 

        try {           
            $collection = new $this->BaseModel;   

            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.sales.index');
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
		
		$objStore = new StoreBatchCardModel;
        $batchNos = $objStore->getBatchNumbers();

        $objMaterial = new StoreRawMaterialModel();
        $materialIds = $objMaterial->getMaterialNumbers();       
        
        $this->ViewData['batchNos']   = $batchNos;
        $this->ViewData['materialIds']   = $materialIds;
        
        ## ALL DATA
        $this->ViewData['sales'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));
        $this->ViewData['fissue_date'] = date('d-m-Y',strtotime($this->ViewData['sales']->issue_date));
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreIssuedMaterialRequest $request, $encID)
    {
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Raw Material, Something went wrong on server.';       

        $id = base64_decode(base64_decode($encID));
        try {

            $collection = $this->BaseModel->find($id);   
            
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.sales.index');
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
        $collection->batch_no        = $request->batch_no;
        $collection->material_id        = $request->material_id;
        $collection->issue_date   = date('Y-m-d',strtotime($request->issue_date));   
        $collection->quantity             = $request->quantity;
        $collection->bill_number             = $request->bill_number;       
        $collection->status             = !empty($request->status) ? 1 : 0;
        ## SAVE DATA
        $collection->save();
        
        return $collection;
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
            0 => 'store_issued_materials.id',
            1 => 'store_issued_materials.id',
            2 => 'store_issued_materials.issue_date',
            3 => 'store_issued_materials.material_id',
            4 => 'store_raw_materials.name',
            5 => 'products.name',
            6 => 'store_issued_materials.quantity',
            7 => 'store_issued_materials.bill_number',
            8 => 'store_issued_materials.status',            
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY 
        //$modelQuery =  $this->BaseModel;
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_issued_materials.id, store_issued_materials.batch_no, store_issued_materials.material_id, store_issued_materials.issue_date, store_issued_materials.quantity,store_issued_materials.bill_number, store_issued_materials.status, store_raw_materials.id as material_code, store_raw_materials.name, store_batch_cards.product_code,products.name as prod_name, products.code as prod_code')        
        ->leftjoin('store_raw_materials', 'store_raw_materials.id' , '=', 'store_issued_materials.material_id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_issued_materials.batch_no')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code');
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
           if (!empty($request->custom['item_code'])) 
           {
                $custom_search = true;
                $key = $request->custom['item_code'];                
                $modelQuery = $modelQuery
                ->where('store_issued_materials.material_id', $key);

           }
           if (!empty($request->custom['material_id'])) 
            {
                $custom_search = true;
                $key = $request->custom['material_id'];                
                $modelQuery = $modelQuery
                ->where('store_issued_materials.material_id', $key);

            }
            if (!empty($request->custom['quantity'])) 
            {
                $custom_search = true;

                $key = $request->custom['quantity'];
                //dd($key);
                $modelQuery = $modelQuery
                ->where('store_issued_materials.quantity',  'LIKE', '%'.$key.'%');

            }
            if (!empty($request->custom['product_name'])) 
            {
                $custom_search = true;

                $key = $request->custom['product_name'];
                //dd($key);
                $modelQuery = $modelQuery
                ->where('products.name',  'LIKE', '%'.$key.'%');

            }
            if (!empty($request->custom['bill_number'])) 
            {
                $custom_search = true;

                $key = $request->custom['bill_number'];
                //dd($key);
                $modelQuery = $modelQuery
                ->where('store_issued_materials.bill_number',  'LIKE', '%'.$key.'%');

            }           
        }

        if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];

                 $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('store_raw_materials.name', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_issued_materials.quantity', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_issued_materials.bill_number', 'LIKE', '%'.$search.'%');
                    $query->orwhere('products.name', 'LIKE', '%'.$search.'%');  
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_issued_materials.status', 'ASC');
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get(['store_issued_materials.id', 
            'store_issued_materials.issue_date',
            'store_issued_materials.material_id',
            'store_issued_materials.quantity',
            'store_issued_materials.bill_number',            
            'store_issued_materials.status',            
            'store_raw_materials.name',
            'prod_code',
            'prod_name',            
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="sales[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['issue_date'] = date('d M Y',strtotime($row->issue_date));
				//$data[$key]['item_code']  = $row->material_id;
				$data[$key]['name']  = $row->name;
                $data[$key]['product_name']  = $row->prod_name;
                $data[$key]['quantity']  =  $row->quantity;
                $data[$key]['bill_number']  =  $row->bill_number;                

                if($row->status==1){
                    $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                }elseif($row->status==0) {
                 $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Inactive</span>';
                }                
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';
                //$data[$key]['actions'] = '';               
                $data[$key]['actions'] =  '<div class="text-center">'.$edit.'</div>';
               

         }
     }
	$objMaterial = new StoreRawMaterialModel;
    $materialIds = $objMaterial->getMaterialNumbers();
    
    
    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    $material_id_string = '<select name="material_id" id="material-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Material</option>';
    foreach ($materialIds as $mval) {
    $material_id_string .='<option class="theme-black blue-select" value="'.$mval['id'].'" '.( $request->custom['material_id'] == $mval['id'] ? 'selected' : '').' >'.$mval['name'].'</option>';
    }
    $material_id_string .='</select>';
    $searchHTML['issue_date']     =  '';    
    //$searchHTML['item_code']     =  '<input type="text" class="form-control" id="item-code" value="'.($request->custom['item_code']).'" placeholder="Search...">';
    $searchHTML['name'] = $material_id_string;
    $searchHTML['product_name'] = '<input type="text" class="form-control" id="product-name" value="'.($request->custom['product_name']).'" placeholder="Search...">';
    $searchHTML['quantity']     =  '<input type="text" class="form-control" id="quantity" value="'.($request->custom['quantity']).'" placeholder="Search...">';
    $searchHTML['bill_number'] = '<input type="text" class="form-control" id="bill-number" value="'.($request->custom['bill_number']).'" placeholder="Search...">';

    $searchHTML['status']   =  '';

    $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';

    /*if ($custom_search) 
    {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return removeSearch(this)" class="btn btn-danger">Remove Filter</a></div>';
    }
    else
    {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary">Search</a></div>';
    }*/

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
    $this->JsonData['msg'] = 'Failed to delete materials, Something went wrong on server.';

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
            
            $module = "non_material_module";
            if(!empty($material_id)){
                $html       = self::_getBatchMaterials($batch_id,$material_id,$module);
            }else{
                $html       = self::_getBatchMaterials($batch_id,false,$module);
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

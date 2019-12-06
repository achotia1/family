<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreRawMaterialModel;
use App\Models\StoreInMaterialModel;

use App\Http\Requests\Admin\StoreRawMaterialRequest;
use App\Traits\GeneralTrait;

class StoreRawMaterialController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreRawMaterialModel $StoreRawMaterialModel,
        StoreInMaterialModel $StoreInMaterialModel
    )
    {
        $this->BaseModel  = $StoreRawMaterialModel;
        $this->StoreInMaterialModel  = $StoreInMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Raw Material';
        $this->ModuleView  = 'admin.materials.';
        $this->ModulePath = 'admin.materials.';

        ## PERMISSION MIDDELWARE
        $this->middleware(['permission:store-material-listing'], ['only' => ['getRecords']]);
        $this->middleware(['permission:store-material-add'], ['only' => ['edit','update','create','store','bulkDelete']]);
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
        // dd('test',$this->ModulePath);
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreRawMaterialRequest $request)
    {        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Material, Something went wrong on server.'; 

        try {           
            $collection = new $this->BaseModel;   

            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.materials.index');
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
        /*$data = $this->BaseModel->where('store_raw_materials.id', base64_decode(base64_decode($encID)))->where('store_raw_materials.company_id', $companyId)->first();
        */
        
        $data = $this->BaseModel
        ->with([   
            'hasInMaterials' 
        ])->where('company_id', $companyId)
        ->find(base64_decode(base64_decode($encID)));
        if(empty($data)) {            
            return redirect()->route('admin.materials.index');
        }
        //dd($data);
        ## ALL DATA
        //$this->ViewData['material'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));
        $this->ViewData['material'] = $data;
              
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreRawMaterialRequest $request, $encID)
    {
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Raw Material, Something went wrong on server.';       

        $id = base64_decode(base64_decode($encID));
        try {

            $collection = $this->BaseModel->find($id);   
            
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.materials.index');
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
        $collection->name           = $request->name;
        $collection->moq            = $request->moq;
        $collection->unit           = $request->unit;        
        /*$collection->balance_stock  = $request->balance_stock; */
        $collection->material_type  = $request->material_type;       
        $collection->status         = !empty($request->status) ? 1 : 0;
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
            0 => 'store_raw_materials.id',
            1 => 'store_raw_materials.id',
            2 => 'store_raw_materials.name',
            3 => 'total_balance',            
            4 => 'store_raw_materials.material_type',
            5 => 'store_raw_materials.moq',
            6 => 'store_raw_materials.status',           
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY 
        /*$modelQuery =  $this->BaseModel
       ->selectRaw('store_raw_materials.id, store_raw_materials.name, store_raw_materials.moq, store_raw_materials.unit,store_raw_materials.price_per_unit, store_raw_materials.total_price, store_raw_materials.opening_stock, store_raw_materials.balance_stock,store_raw_materials.trigger_qty,store_raw_materials.status,  store_issued_materials.quantity as issued_quantity')        
        ->leftjoin('store_issued_materials', 'store_issued_materials.material_id' , '=', 'store_raw_materials.id');*/
        $companyId = self::_getCompanyId();
        /*$modelQuery =  $this->BaseModel
        ->where('store_raw_materials.company_id', $companyId);*/
        $modelQuery =  $this->BaseModel
       ->selectRaw('store_raw_materials.id, store_raw_materials.name, store_raw_materials.moq, store_raw_materials.unit, store_raw_materials.material_type,store_raw_materials.status, SUM(store_in_materials.lot_balance) as total_balance')
        ->leftjoin('store_in_materials', 'store_in_materials.material_id' , '=', 'store_raw_materials.id')
        ->where('store_raw_materials.company_id', $companyId)
        ->where('store_in_materials.deleted_at', null);
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['name'])) 
            {
                $custom_search = true;
                $key = $request->custom['name'];                
                $modelQuery = $modelQuery
                ->where('store_raw_materials.name', 'LIKE', '%'.$key.'%');

            }

            /*if (!empty($request->custom['balance_stock'])) 
            {
                $custom_search = true;
                $key = $request->custom['balance_stock'];               
                $modelQuery = $modelQuery
                ->where('store_raw_materials.balance_stock', 'LIKE', '%'.$key.'%');

            }*/            
            if (isset($request->custom['material_type'])) 
            {
                $custom_search = true;
                $key = $request->custom['material_type'];
                $modelQuery = $modelQuery
                ->where('store_raw_materials.material_type', $key);
            }
            if (isset($request->custom['moq'])) 
            {
                $custom_search = true;
                $key = $request->custom['moq'];
                $modelQuery = $modelQuery
                ->where('store_raw_materials.moq', '>', $key);
            }
            if (isset($request->custom['status'])) 
            {
                $custom_search = true;
                $key = $request->custom['status'];
                $modelQuery = $modelQuery
                ->where('store_raw_materials.status', $key);
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
                    $query->orwhere('store_raw_materials.moq', 'LIKE', '%'.$search.'%');
                    
                    $query->orwhere('store_raw_materials.unit', 'LIKE', '%'.$search.'%');   
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_raw_materials.id', 'DESC');
                        
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $modelQuery =  $modelQuery->skip($start)
        ->take($length)
        ->groupBy('store_in_materials.material_id');
        if (!empty($request->custom['total_balance'])) 
        {
            $custom_search = true;
            $key = $request->custom['total_balance'];                
            $modelQuery = $modelQuery
            ->havingRaw('sum(store_in_materials.lot_balance) > '.$key );
        }
        /*$object = $modelQuery->skip($start)
        ->take($length)
        ->get();*/ 
        $object = $modelQuery
         ->get(); 

        /*['store_raw_materials.id', 
            'store_raw_materials.name', 
            'store_raw_materials.moq',            
            'store_raw_materials.unit',
            'store_raw_materials.material_type',             
            'store_raw_materials.balance_stock', 
            'store_raw_materials.status',            
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['name']  = '<span title="'.ucfirst($row->name).'">'.str_limit(ucfirst($row->name), '60', '...').'</span>';
                
                $data[$key]['total_balance']  =  !empty($row->total_balance) ? number_format($row->total_balance, 2, '.', '').' '.$row->unit : '0.00'. ' '.$row->unit;

                $data[$key]['material_type']  =  $row->material_type. " Material";
                $data[$key]['moq']  =  number_format($row->moq, 2, '.', '');              

                if($row->status==1){
                    $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                }elseif($row->status==0) {
                 $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Inactive</span>';
                }
                
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';

                $data[$key]['actions'] = '';

                if(auth()->user()->can('store-material-add'))
                {
                    $data[$key]['actions'] =  '<div class="text-center">'.$edit.'</div>';
                }

         }
     }

    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    $searchHTML['name']     =  '<input type="text" class="form-control" id="name" value="'.($request->custom['name']).'" placeholder="Search...">';   
    $searchHTML['total_balance']   =  '<input type="text" class="form-control" id="total-balance" value="'.($request->custom['total_balance']).'" placeholder="More than...">';
    
    $searchHTML['material_type']   =  '<select name="material_type" id="material-type" class="form-control my-select">
            <option class="theme-black blue-select" value="">Material Type</option>
            <option class="theme-black blue-select" value="Raw" '.( $request->custom['material_type'] == "Raw" ? 'selected' : '').' >Raw Material</option>
            <option class="theme-black blue-select" value="Packaging" '.( $request->custom['material_type'] == "Packaging" ? 'selected' : '').'>Packaging Material</option>
            <option class="theme-black blue-select" value="Consumable" '.( $request->custom['material_type'] == "Consumable" ? 'selected' : '').'>Consumable Material</option>            
            </select>';
    $searchHTML['moq']   =  '<input type="text" class="form-control" id="moq" value="'.($request->custom['moq']).'" placeholder="More than...">';   
    $searchHTML['status']   =  '<select name="status" id="search-status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Status</option>
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').' >Active</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').'>Inactive</option>            
            </select>';
   
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
   
    $this->JsonData['status'] = 'error';
    $this->JsonData['msg'] = 'Failed to delete materials, Something went wrong on server.';

    if (!empty($request->arrEncId)) 
    {
        $arrID = array_map(function($item)
        {
            return base64_decode(base64_decode($item));

        }, $request->arrEncId);
        
        // dd($arrID);
        $store_in_count = $this->StoreInMaterialModel->whereIn('material_id',$arrID)->count();
        if($store_in_count>0) 
        {
            $this->JsonData['status'] = __('admin.RESP_ERROR');
            $this->JsonData['msg'] = 'Cant delete this material which is assigned in Material In Module'; 
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
}

}

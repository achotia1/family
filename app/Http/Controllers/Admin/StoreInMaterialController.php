<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreInMaterialModel;
use App\Models\StoreRawMaterialModel;


use App\Http\Requests\Admin\StoreInMaterialRequest;
use App\Traits\GeneralTrait;

class StoreInMaterialController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreInMaterialModel $StoreInMaterialModel
    )
    {
        $this->BaseModel  = $StoreInMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'In Material';
        $this->ModuleView  = 'admin.store-in-material.';
        $this->ModulePath = 'admin.materials-in.';

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
        //$objLot = new StoreBatchCardModel;
        $lotNo = $this->BaseModel->geLotNo();
        $this->ViewData['lotNo']   = $lotNo;

        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getMaterialNumbers();
        $this->ViewData['materialIds']   = $materialIds;

        // dd('test',$this->ModulePath);
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreInMaterialRequest $request)
    {        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Material, Something went wrong on server.'; 

        try {           
            $collection = new $this->BaseModel;
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                ## ADD Lot Quantity in material balance
                $objMaterial = new StoreRawMaterialModel;
                $rawMaterialcollection = $objMaterial->find($request->material_id);
                $rawMaterialcollection->balance_stock = $rawMaterialcollection->balance_stock + $request->lot_qty;
                $rawMaterialcollection->save();
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.materials-in.index');
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
        $data = $this->BaseModel->where('store_in_materials.id', base64_decode(base64_decode($encID)))->where('store_in_materials.company_id', $companyId)->first();
        if(empty($data)) {            
            return redirect()->route('admin.materials-in.index');
        }
        
        ## ALL DATA        
        $this->ViewData['material'] = $data;
        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getMaterialNumbers();
        $this->ViewData['materialIds']   = $materialIds;      
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreInMaterialRequest $request, $encID)
    {
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update record, Something went wrong on server.';        
        $id = base64_decode(base64_decode($encID));
        try {
            $collection = $this->BaseModel->find($id);
            $preLotQty =  $collection->lot_qty;                
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                ## UPDATE Lot Quantity in material balance
                $objMaterial = new StoreRawMaterialModel;
                $rawMaterialcollection = $objMaterial->find($request->material_id);
               
                $rawMaterialcollection->balance_stock = ($rawMaterialcollection->balance_stock - $preLotQty) + $request->lot_qty;                
                $rawMaterialcollection->save();

                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.materials-in.index');
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
        if(!$collection->id){
            $collection->lot_balance     = $request->lot_qty;
        }
        $collection->company_id        = self::_getCompanyId();
        $collection->material_id        = $request->material_id;
        $collection->lot_no   = $request->lot_no;
        $collection->lot_qty             = $request->lot_qty;        
        $collection->price_per_unit             = $request->price_per_unit;
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
            0 => 'store_in_materials.id',
            1 => 'store_in_materials.id',
            2 => 'store_in_materials.lot_no',
            3 => 'store_in_materials.material_id',            
            4 => 'store_in_materials.lot_qty',
            5 => 'store_in_materials.lot_balance',
            6 => 'store_in_materials.status',           
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY         
        $companyId = self::_getCompanyId();
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_in_materials.id, store_in_materials.material_id, store_in_materials.lot_no, store_in_materials.lot_qty,store_in_materials.price_per_unit, store_in_materials.lot_balance, store_in_materials.status, store_raw_materials.name')       
        ->leftjoin('store_raw_materials', 'store_raw_materials.id' , '=', 'store_in_materials.material_id')
        ->where('store_in_materials.company_id', $companyId);
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['lot_no'])) 
            {
                $custom_search = true;
                $key = $request->custom['lot_no'];                
                $modelQuery = $modelQuery
                ->where('store_in_materials.lot_no', 'LIKE', '%'.$key.'%');

            }
            if (!empty($request->custom['material_id'])) 
            {
                $custom_search = true;
                $key = $request->custom['material_id'];                
                $modelQuery = $modelQuery
                ->where('store_in_materials.material_id', $key);

            }

            if (!empty($request->custom['lot_qty'])) 
            {
                $custom_search = true;
                $key = $request->custom['lot_qty'];               
                $modelQuery = $modelQuery
                ->where('store_in_materials.lot_qty', 'LIKE', '%'.$key.'%');

            }            
            if (isset($request->custom['lot_balance'])) 
            {
                $custom_search = true;
                $key = $request->custom['lot_balance'];
                $modelQuery = $modelQuery
                ->where('store_in_materials.lot_balance', $key);
            }            
            if (isset($request->custom['status'])) 
            {
                $custom_search = true;
                $key = $request->custom['status'];
                $modelQuery = $modelQuery
                ->where('store_in_materials.status', $key);
            }
        }

        if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];

                 $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('store_in_materials.lot_no', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_raw_materials.name', 'LIKE', '%'.$search.'%');

                    $query->orwhere('store_in_materials.lot_qty', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_in_materials.lot_balance', 'LIKE', '%'.$search.'%');   
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_in_materials.status', 'ASC');
                        
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get(['store_in_materials.id', 
            'store_in_materials.material_id', 
            'store_in_materials.lot_no',            
            'store_in_materials.lot_qty',
            'store_in_materials.price_per_unit',             
            'store_in_materials.lot_balance',
            'store_in_materials.status',
            'store_raw_materials.name',            
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_in_materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['lot_no']  = $row->lot_no;
                $data[$key]['material_id']  =  $row->name;
                $data[$key]['lot_qty']  =  $row->lot_qty;
                $data[$key]['lot_balance']  =  $row->lot_balance;              

                if($row->status==1){
                    $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                }elseif($row->status==0) {
                 $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Closed</span>';
                }
                
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';

                $data[$key]['actions'] = '';

                /*if(auth()->user()->can('material-add'))
                {*/
                    $data[$key]['actions'] =  '<div class="text-center">'.$edit.'</div>';
                /*}*/

        }
    }

    $objMaterial = new StoreRawMaterialModel;
    $materialIds = $objMaterial->getMaterialNumbers();
    
    $material_id_string = '<select name="material_id" id="material-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Material</option>';
        foreach ($materialIds as $mval) {
        $material_id_string .='<option class="theme-black blue-select" value="'.$mval['id'].'" '.( $request->custom['material_id'] == $mval['id'] ? 'selected' : '').' >'.$mval['name'].'</option>';
        }
    $material_id_string .='</select>';

    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    $searchHTML['lot_no']     =  '<input type="text" class="form-control" id="lot-no" value="'.($request->custom['lot_no']).'" placeholder="Search...">';
    $searchHTML['material_id']     =  $material_id_string;
    $searchHTML['lot_qty']   =  '<input type="text" class="form-control" id="lot-qty" value="'.($request->custom['lot_qty']).'" placeholder="Search...">';
    $searchHTML['lot_balance']   =  '<input type="text" class="form-control" id="lot-balance" value="'.($request->custom['lot_balance']).'" placeholder="Search...">';
    //$searchHTML['status']   =  '';  
    $searchHTML['status']   =  '<select name="status" id="search-status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Status</option>
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').' >Active</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').'>Closed</option>            
            </select>';
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

}
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreInMaterialModel;
use App\Models\StoreRawMaterialModel;
use App\Models\ProductionHasMaterialModel;
use App\Models\StoreLotCorrectionModel;

use App\Http\Requests\Admin\StoreInMaterialRequest;
use App\Http\Requests\Admin\StoreCorrectMaterialRequest;

use App\Traits\GeneralTrait;
use Carbon\Carbon;

class StoreInMaterialController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreInMaterialModel $StoreInMaterialModel,
        ProductionHasMaterialModel $ProductionHasMaterialModel
    )
    {
        $this->BaseModel  = $StoreInMaterialModel;
        $this->ProductionHasMaterialModel  = $ProductionHasMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'In Material';
        $this->ModuleView  = 'admin.store-in-material.';
        $this->ModulePath = 'admin.materials-in.';

        ## PERMISSION MIDDELWARE
        $this->middleware(['permission:store-material-in-listing'], ['only' => ['getRecords']]);
        $this->middleware(['permission:store-material-in-add'], ['only' => ['edit','update','create','store','bulkDelete']]);
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

        $company_id = self::_getCompanyId();

        //$objLot = new StoreBatchCardModel;
        $lotNo = $this->BaseModel->geLotNo();
        $this->ViewData['lotNo']   = $lotNo;

        $objMaterial = new StoreRawMaterialModel;
        $materialIds = $objMaterial->getMaterialNumbers($company_id);

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
        $materialIds = $objMaterial->getMaterialNumbers($companyId);
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
        } else {
            $diffQty = $request->lot_qty - $collection->lot_qty;
            $collection->lot_balance = $collection->lot_balance + $diffQty; 
        }
        $collection->company_id        = self::_getCompanyId();
        $collection->user_id        = auth()->user()->id;
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
                ->where('store_in_materials.lot_qty', '>', $key);

            }            
            if (isset($request->custom['lot_balance'])) 
            {
                $custom_search = true;
                $key = $request->custom['lot_balance'];
                $modelQuery = $modelQuery
                ->where('store_in_materials.lot_balance', '>', $key);
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
            $modelQuery = $modelQuery->orderBy('store_in_materials.status', 'DESC')->orderBy('store_in_materials.id', 'DESC');
                        
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get();  

       /* ['store_in_materials.id', 
            'store_in_materials.material_id', 
            'store_in_materials.lot_no',            
            'store_in_materials.lot_qty',
            'store_in_materials.price_per_unit',             
            'store_in_materials.lot_balance',
            'store_in_materials.status',
            'store_raw_materials.name',            
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_in_materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['lot_no']  = $row->lot_no;
                $data[$key]['material_id']  =  $row->name;
                $data[$key]['lot_qty']  =  number_format($row->lot_qty, 2, '.', '');
                $data[$key]['lot_balance']  =  number_format($row->lot_balance, 2, '.', '');           

                if($row->status==1){
                    $data[$key]['status'] = 'Active';
                }elseif($row->status==0) {
                 $data[$key]['status'] = 'Inactive';
                }
                
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';
                $view = '<a href="'.route($this->ModulePath.'correct-balance',[ base64_encode(base64_encode($row->id))]).'" title="Correct Balance"><span class="glyphicon glyphicon-ok-circle"></a>';
                
                $data[$key]['actions'] = '';
                if(auth()->user()->can('store-material-in-add'))
                {
                    $data[$key]['actions'] =  '<div class="text-center">'.$edit.' '.$view.'</div>';
                }

        }
    }

    $objMaterial = new StoreRawMaterialModel;
    $materialIds = $objMaterial->getMaterialNumbers($companyId);
    
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
    $searchHTML['lot_qty']   =  '<input type="text" class="form-control" id="lot-qty" value="'.($request->custom['lot_qty']).'" placeholder="More than...">';
    $searchHTML['lot_balance']   =  '<input type="text" class="form-control" id="lot-balance" value="'.($request->custom['lot_balance']).'" placeholder="More than...">';
    //$searchHTML['status']   =  '';  
    $searchHTML['status']   =  '<select name="status" id="search-status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Status</option>
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').' >Active</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').'>Inactive</option>            
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
        $data = $this->BaseModel->with([
            'hasMateials'            
        ])->where('company_id', $companyId)
        ->find($id);
      
        if(empty($data)) {            
            return redirect()->route('admin.materials-in.index');
        } 
        $this->ViewData['material'] = $data;       
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'correct', $this->ViewData);
       
}
public function updateBalance(StoreCorrectMaterialRequest $request)
    {
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update record, Something went wrong on server.';
        try 
        {           
            $id = $request->id;
            $collection = $this->BaseModel->find($id);
            $collection->lot_balance = $request->corrected_balance;
            $collection->balance_corrected_at = Carbon::today();
            $collection->save();
            /*$id = $request->id;
            $previous_balance = $request->previous_balance;
            $corrected_balance = $request->corrected_balance;*/
            $correctObj = new StoreLotCorrectionModel;
            $correctObj->user_id = auth()->user()->id;
            $correctObj->lot_id = $id;
            $correctObj->previous_balance = $request->previous_balance;
            $correctObj->corrected_balance = $request->corrected_balance;
            if($correctObj->save()){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.materials-in.index');
                $this->JsonData['msg'] = 'Lot Balance updated successfully.';
            }
        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);        
    }
}
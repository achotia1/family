<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreInMaterialModel;
use App\Models\StoreRawMaterialModel;
use App\Models\ProductionHasMaterialModel;
use App\Models\StoreLotCorrectionModel;
use App\Models\StoreMaterialOpeningModel;

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
        // ASHVINI       

        // END ASHVINI
        //$objLot = new StoreBatchCardModel;
        $lotNo = $this->BaseModel->geLotNo($company_id);
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
        //dd($request->all());
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

                ## ADD OPENING BAL IN OPENING TABLE
                $is_opening = !empty($request->status) ? 0 : 1;
                if($is_opening == 0){
                    $openingDate =  Carbon::today()->format('Y-m-d');
                    $objMatOpen = new StoreMaterialOpeningModel;;
                    $rawopncollection = $objMatOpen->where('material_id', $request->material_id)->where('opening_date',$openingDate)->first();
                    if(!empty($rawopncollection)){
                        $rawopncollection->opening_bal = $rawopncollection->opening_bal+$request->lot_qty;
                        $rawopncollection->save();
                    } else {
                        $objMatOpen->material_id = $request->material_id;
                        $objMatOpen->opening_bal = $request->lot_qty;
                        $objMatOpen->opening_date = $openingDate;
                        $objMatOpen->save();
                    }
                }
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
        ## ASHVINI
        /*$flagEditOpening = 0;
        $todaysDate =  Carbon::today()->format('Y-m-d');
        $createdDate = $data->created_at;
        $fcreatedDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdDate)
                ->format('Y-m-d');
        //$fcreatedDate = '2020-01-10';
        if($todaysDate == $fcreatedDate){
            if($data->status == 0)
                $flagEditOpening = 1;
        } else if($fcreatedDate < $todaysDate){
            $flagEditOpening = 1;
        }
        echo $flagEditOpening;

        dd($data);*/
        ## END ASHVINI
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
            $preStatus =  $collection->status;
            $prevMid = $collection->material_id;            
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                ## UPDATE Lot Quantity in material balance
                $objMaterial = new StoreRawMaterialModel;
                $rawMaterialcollection = $objMaterial->find($request->material_id);
                $rawMaterialcollection->balance_stock = ($rawMaterialcollection->balance_stock - $preLotQty) + $request->lot_qty;                
                $rawMaterialcollection->save();
                ## IF LOT IS TODAYS OPENING, ANY OLDER THEN EDIT OPENING BALANCE IN store_material_openings TABLE
                //$is_opening = !empty($request->status) ? 0 : 1;
                $flagEditOpening = 0;
                $flagSubstarctPrev = 1;
                $flagOlder = 0;
                $todaysDate =  Carbon::today()->format('Y-m-d');
                $createdDate = $collection->created_at;
                $fcreatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $createdDate)
                        ->format('Y-m-d');                
                if($todaysDate == $fcreatedDate){
                    if($collection->status == 0){
                        $flagEditOpening = 1;
                        ## IF PREV STATUS WAS NORMAL AND IT IS EDITED TO OPENING
                        ## DO NOT SUBSTRACT THE PREV BAL
                        if($preStatus == 1 && $collection->status == 0)
                            $flagSubstarctPrev = 0;                        
                    }
                    ## IF PREVIOUS BATCH WAS OPENING AND NOW IT IS NORMAIL
                    ## SUBSTRACT FROM OPENING BAL
                    if($preStatus == 0 && $collection->status == 1){
                        $flagEditOpening = 1;
                        $flagSubstarctPrev = 2;
                    }
                } else if($fcreatedDate < $todaysDate){
                    $flagEditOpening = 1;
                    $flagOlder = 1;
                }                
                if($flagEditOpening == 1){
                    ## IF RECORD IS ANY OLDER THEN UPDATE ALL THE OPENINGS GREATER THAN CREATED DATE 
                    if($flagOlder == 1){
                        //$objMattOpen = new StoreMaterialOpeningModel;
                        $prevArr[$prevMid][$id] = $preLotQty;
                        $currArr[$request->material_id][$id] = $request->lot_qty;
                        $objMattOpen = new StoreMaterialOpeningModel;
                        if($collection->status == 0){
                            $objMattOpen->updateOpeningBalsNew($fcreatedDate, $currArr, $prevArr, true);
                        } else {
                            $objMattOpen->updateOpeningBalsNew($fcreatedDate, $currArr, $prevArr);    
                        }
                    } else {
                        $openingDate =  Carbon::today()->format('Y-m-d');
                        $objMatOpen = new StoreMaterialOpeningModel;
                        $rawopncollection = $objMatOpen->where('material_id', $collection->material_id)->where('opening_date',$openingDate)->first();
                        if(!empty($rawopncollection)){                       
                            if($flagSubstarctPrev == 1)
                                $rawopncollection->opening_bal = ($rawopncollection->opening_bal - $preLotQty) + $request->lot_qty;
                            else if($flagSubstarctPrev == 2)
                                $rawopncollection->opening_bal = $rawopncollection->opening_bal - $preLotQty;
                            else
                                $rawopncollection->opening_bal = $rawopncollection->opening_bal + $request->lot_qty;

                            $rawopncollection->save();
                        } else {
                            $objMatOpen->material_id = $collection->material_id;
                            $objMatOpen->opening_bal = $request->lot_qty;
                            $objMatOpen->opening_date = $openingDate;
                            $objMatOpen->save();
                        }
                    }
                }                
                
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

    public function _storeOrUpdate($collection, $request)
    {        
        $is_opening = !empty($request->status) ? 0 : 1;
        //$mytime->toDateTimeString();
        
        if(!$collection->id){
            $collection->lot_balance     = $request->lot_qty;
            /*if($is_opening == 0)
                $collection->created_at = new Carbon('-2 days');*/
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
        $collection->status             = !empty($request->status) ? 0 : 1;      
        
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
            3 => 'store_raw_materials.name',            
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
            $modelQuery = $modelQuery->orderBy('store_in_materials.id', 'DESC');
                        
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_in_materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['lot_no']  = $row->lot_no;
                $data[$key]['material_id']  =  $row->name;
                $data[$key]['lot_qty']  =  number_format($row->lot_qty, 2, '.', '');
                $data[$key]['lot_balance']  =  number_format($row->lot_balance, 2, '.', '');           

                if($row->status==1){
                    $data[$key]['status'] = 'Received';
                }elseif($row->status==0) {
                 $data[$key]['status'] = 'Opening';
                }
                
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';
                $view = '<a href="'.route($this->ModulePath.'correct-balance',[ base64_encode(base64_encode($row->id))]).'" title="Correct Balance"><span class="glyphicon glyphicon-ok-circle"></a>';
                $delete = '<a href="javascript:void(0)" onclick="return deleteCollection(this)" data-href="'.route($this->ModulePath.'destroy', [base64_encode(base64_encode($row->id))]) .'" title="Delete"><span class="glyphicon glyphicon-trash"></span></a>';    
                
                $data[$key]['actions'] = '';
                if(auth()->user()->can('store-material-in-add'))
                {
                    $data[$key]['actions'] =  '<div class="text-center">'.$edit.' '.$delete.' '.$view.'</div>';
                }

        }
    }

    $objMaterial = new StoreRawMaterialModel;
    $materialIds = $objMaterial->getMaterialNumbers($companyId);
    
    $material_id_string = '<select name="material_id" id="material-id" class="form-control my-select select2"><option class="theme-black blue-select" value="">Select Material</option>';
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
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').' >Received</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').'>Opening</option>            
            </select>';
    

    /*if ($custom_search) 
    {*/
        /*$seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return removeSearch(this)" class="btn btn-danger"><span class="fa  fa-remove"></span></a></div>';*/
     /*}
     else
     {*/
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

public function destroy($encID)
{
    //dd($request->all());
    $this->JsonData['status'] = 'error';
    $this->JsonData['msg'] = 'Failed to delete material in record, Something went wrong on server.';

    $id = base64_decode(base64_decode($encID));

    if (!empty($id)) 
    {

        $store_lot_count = $this->ProductionHasMaterialModel->where('lot_id',$id)->count();
        if($store_lot_count>0) 
        {
            $this->JsonData['status'] = __('admin.RESP_ERROR');
            $this->JsonData['msg'] = 'Cant delete this Lot which is assigned in Production Module'; 
            return response()->json($this->JsonData);
            exit();
        }

        try 
        {

            if ($this->BaseModel->where('id', $id)->delete()) 
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
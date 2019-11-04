<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


// models
use App\Models\StoreProductionModel;
use App\Models\StoreBatchCardModel;
use App\Models\StoreRawMaterialModel;

use App\Http\Requests\Admin\StoreProductionRequest;
use App\Traits\GeneralTrait;

class StoreProductionController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreProductionModel $StoreProductionModel
    )
    {
        $this->BaseModel  = $StoreProductionModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Production';
        $this->ModuleView  = 'admin.store-production.';
        $this->ModulePath = 'admin.production.';

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

        $objStore = new StoreBatchCardModel();
        $batchNos = $objStore->getBatchNumbers();

        $objMaterial = new StoreRawMaterialModel();
        $materialIds = $objMaterial->getMaterialNumbers();
        /*$arrBatchNos = array();
        foreach($batchNos as $val){
            $arrBatchNos[$val['id']] = $val['batch_card_no'];
        }*/
        $this->ViewData['batchNos']   = $batchNos;
        $this->ViewData['materialIds']   = $materialIds;
        //dd($arrBatchNos);
        // view file with data
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreProductionRequest $request)
    {        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Batch, Something went wrong on server.';
        try {

            $collection = new $this->BaseModel;
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.production.index');
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

        $objStore = new StoreBatchCardModel();
        $batchNos = $objStore->getBatchNumbers();

        $objMaterial = new StoreRawMaterialModel();
        $materialIds = $objMaterial->getMaterialNumbers();

        $this->ViewData['batchNos']   = $batchNos;
        $this->ViewData['materialIds']   = $materialIds;

        // All data
        $this->ViewData['production'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));

        // view file with data
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreProductionRequest $request, $encID)    {
        
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Branch, Something went wrong on server.';       

        $id = base64_decode(base64_decode($encID));
        try {

            $collection = $this->BaseModel->find($id);   
            
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.production.index');
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
        $collection->material_id   = $request->material_id;
        $collection->quantity             = $request->quantity;
        $collection->unit             = $request->unit;        
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
            0 => 'store_productions.id',
            1 => 'store_productions.id',
            2 => 'store_productions.batch_no',
            3 => 'store_productions.material_id',
            4 => 'store_productions.quantity',
            5 => 'store_productions.unit',
            6 => 'store_productions.status    ',           
        );

        /*--------------------------------------
        |  Model query and filter
        ------------------------------*/

        // start model query        
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_productions.id, store_productions.batch_no, store_productions.material_id, store_productions.quantity,store_productions.unit, store_productions.status, store_batch_cards.batch_card_no, store_raw_materials.name')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_productions.batch_no')
        ->leftjoin('store_raw_materials', 'store_raw_materials.id' , '=', 'store_productions.material_id');
        // get total count 
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        //dd($request->custom);
        // filter options
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['batch_no'])) 
            {
                $custom_search = true;

                $key = $request->custom['batch_no'];
                //dd($key);
                $modelQuery = $modelQuery
                ->where('store_productions.batch_no',  $key);

            }

            if (!empty($request->custom['material_id'])) 
            {
                $custom_search = true;
                $key = $request->custom['material_id'];                
                $modelQuery = $modelQuery
                ->where('store_productions.material_id', $key);

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
                    $query->orwhere('store_raw_materials.name', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_productions.quantity', 'LIKE', '%'.$search.'%');   
                });              

            }
        }

            // get total filtered
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

            // offset and limit
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_productions.status', 'ASC'); 
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get(['store_productions.id', 
            'store_productions.batch_no', 
            'store_productions.material_id', 
            'store_productions.quantity',
            'store_productions.unit',
            'store_productions.status',
            'store_batch_cards.batch_card_no',
            'store_raw_materials.name',         
        ]);  

        //dd($object);
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_productions[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['batch_no']  = $row->batch_card_no;

                $data[$key]['material_id']  =  $row->name;
                $data[$key]['quantity']  =  $row->quantity;
                $data[$key]['unit']  =  $row->unit;
                if($row->status==1){
                    $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                }elseif($row->status==0) {
                 $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Inactive</span>';
                }                
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';

                $data[$key]['actions'] = '';

                //if(auth()->user()->can('batch-add'))
                //{
                    $data[$key]['actions'] =  '<div class="text-center">'.$edit.'</div>';
                //}

         }
     }
    $objStore = new StoreBatchCardModel();
    $batchNos = $objStore->getBatchNumbers();

    $objMaterial = new StoreRawMaterialModel();
    $materialIds = $objMaterial->getMaterialNumbers();

    // search html
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
     
    $batch_no_string = '<select name="batch_no" id="batch-no" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_no'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';
    $searchHTML['batch_no'] = $batch_no_string;

    $material_id_string = '<select name="material_id" id="material-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Material</option>';
        foreach ($materialIds as $mval) {
        $material_id_string .='<option class="theme-black blue-select" value="'.$mval['id'].'" '.( $request->custom['material_id'] == $mval['id'] ? 'selected' : '').' >'.$mval['name'].'</option>';
        }
    $material_id_string .='</select>';
    $searchHTML['batch_no'] = $batch_no_string;
    $searchHTML['material_id'] = $material_id_string;
    $searchHTML['quantity']     =  '';
    $searchHTML['unit']   =  '';     
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
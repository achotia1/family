<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// models
use App\Models\StoreRawMaterialModel;

use App\Http\Requests\Admin\StoreRawMaterialRequest;
use App\Traits\GeneralTrait;

class StoreRawMaterialController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreRawMaterialModel $StoreRawMaterialModel
    )
    {
        $this->BaseModel  = $StoreRawMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Raw Material';
        $this->ModuleView  = 'admin.materials.';
        $this->ModulePath = 'admin.materials.';

        /*$this->middleware(['permission:manage-materials'], ['only' => ['edit','update','create','store','getRecords','bulkDelete']]);*/
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
        // dd('test',$this->ModulePath);
        // view file with data
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
       // Default site settings
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
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['modulePath']   = $this->ModulePath;

        // All data
        $this->ViewData['material'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));

        // view file with data
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
        
        $collection->company_id        = self::_getCompanyId();
        $collection->name        = $request->name;
        $collection->total_qty   = $request->total_qty;
        $collection->unit             = $request->unit;
        $collection->price_per_unit             = $request->price_per_unit;
        $collection->total_price             = $request->total_price;
        $collection->opening_stock             = $request->opening_stock;
        $collection->balance_stock             = $request->balance_stock; 
        $collection->trigger_qty              = $request->trigger_qty;       
        $collection->status             = !empty($request->status) ? 1 : 0;
        //Save data
        $collection->save();
        
        return $collection;
    }

    public function getRecords(Request $request)
    {

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
            0 => 'store_raw_materials.id',
            1 => 'store_raw_materials.id',
            2 => 'store_raw_materials.name',
            3 => 'store_raw_materials.total_qty',
            4 => 'store_raw_materials.unit',
            5 => 'store_raw_materials.price_per_unit',
            6 => 'store_raw_materials.total_price',
            7 => 'store_raw_materials.opening_stock',
            8 => 'store_raw_materials.balance_stock',
            9 => 'store_raw_materials.trigger_qty',
            10 => 'store_raw_materials.status',
        );

        /*--------------------------------------
        |  Model query and filter
        ------------------------------*/

        // start model query        
        $modelQuery =  $this->BaseModel;
       
        // get total count 
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        // filter options
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['name'])) 
            {
                $custom_search = true;

                $key = $request->custom['name'];
                //dd($key);
                $modelQuery = $modelQuery
                ->where('store_raw_materials.name', 'LIKE', '%'.$key.'%');

            }

            if (!empty($request->custom['total_qty'])) 
            {
                $custom_search = true;

                $key = $request->custom['total_qty'];
                //dd($key);
                $modelQuery = $modelQuery
                ->where('store_raw_materials.total_qty', 'LIKE', '%'.$key.'%');

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
                    $query->orwhere('store_raw_materials.total_qty', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('store_raw_materials.unit', 'LIKE', '%'.$search.'%');   
                });              

            }
        }

            // get total filtered
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

            // offset and limit
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_raw_materials.status', 'ASC');           
                //$modelQuery = $modelQuery->orderBy('vehicles.chassis_number', 'ASC');           
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get(['store_raw_materials.id', 
            'store_raw_materials.name', 
            'store_raw_materials.total_qty', 
            'store_raw_materials.price_per_unit',
            'store_raw_materials.unit',
            'store_raw_materials.trigger_qty',
            'store_raw_materials.opening_stock', 
            'store_raw_materials.status', 
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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['name']  = '<span title="'.ucfirst($row->name).'">'.str_limit(ucfirst($row->name), '60', '...').'</span>';

                $data[$key]['total_qty']  =  $row->total_qty. ' '.$row->unit;
                $data[$key]['price_per_unit']  =  $row->price_per_unit;
                $data[$key]['trigger_qty']  =  $row->trigger_qty;

                $data[$key]['opening_stock']  =  $row->opening_stock;
                $data[$key]['received_qty']  =  0;
                $data[$key]['issued_qty']  =  0;
                $data[$key]['return_qty']  =  0;
                $data[$key]['balance_stock']  =  0;

                if($row->status==1){
                    $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                }elseif($row->status==0) {
                 $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Inactive</span>';
                }
                //$view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'"><img src="'.url('/assets/admin/images').'/icons/eye.svg" alt=" view"></a>';
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>';

                $data[$key]['actions'] = '';

                /*if(auth()->user()->can('material-add'))
                {*/
                    $data[$key]['actions'] =  '<div class="text-center">'.$edit.'</div>';
                /*}*/

         }
     }

            // search html
     $searchHTML['id']       =  '';
     $searchHTML['select']   =  '';
     $searchHTML['name']     =  '<input type="text" class="form-control" id="name" value="'.($request->custom['name']).'" placeholder="Search...">';
     $searchHTML['total_qty']     =  '<input type="text" class="form-control" id="total-qty" value="'.($request->custom['total_qty']).'" placeholder="Search...">';
     $searchHTML['unit']   =  '';
     $searchHTML['price_per_unit']   =  '';
     $searchHTML['total_price']   =  '';
     $searchHTML['opening_stock']   =  '';
     $searchHTML['balance_stock']   =  '';
     $searchHTML['trigger_qty']   =  '';
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

}

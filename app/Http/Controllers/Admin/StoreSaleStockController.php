<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreSaleStockModel;


use App\Traits\GeneralTrait;


class StoreSaleStockController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(
        StoreSaleStockModel $StoreSaleStockModel
       
    )
    {
        $this->BaseModel  = $StoreSaleStockModel;
        //$this->ProductionHasMaterialModel  = $ProductionHasMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Sales Stock';
        $this->ModuleView  = 'admin.store-in-stock.';
        $this->ModulePath = 'admin.sale-stock.';

        ## PERMISSION MIDDELWARE
        //$this->middleware(['permission:store-material-in-listing'], ['only' => ['getRecords']]);
        //$this->middleware(['permission:store-material-in-add'], ['only' => ['edit','update','create','store','bulkDelete']]);
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
        ->selectRaw('store_sales_stock.id, store_sales_stock.manufacturing_cost, store_sales_stock.quantity, store_sales_stock.balance_quantity, store_batch_cards.batch_card_no,products.name, products.code')       
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_sales_stock.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_sales_stock.product_id')
        ->where('store_sales_stock.company_id', $companyId);
        
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
        dd($modelQuery->toSql());
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
    /*public function create()
    {}

    public function store(StoreInMaterialRequest $request)
    { }*/

   /* public function show($encID)
    {}*/
}
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StoreOutMaterialModel;
use App\Models\StoreBatchCardModel;
use App\Models\ProductsModel;
use App\Models\StoreInMaterialModel;
use App\Models\StoreRawMaterialModel;
use App\Traits\GeneralTrait;
use Carbon\Carbon;

class ReportController extends Controller
{   
    private $BaseModel;
    use GeneralTrait;
    
    public function __construct(

        StoreOutMaterialModel $StoreOutMaterialModel
    )
    {

        $this->BaseModel = $StoreOutMaterialModel;   

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Reports';
        $this->ModuleView  = 'admin.reports.';
        $this->ModulePath  = 'admin.reports.';
        
    }

    public function batchIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Batch-Wise Report';
        $this->ViewData['moduleAction'] = 'Batch-Wise Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        

        // view file with data
        return view($this->ModuleView.'batches',$this->ViewData);
    }

    public function getBatchRecords(Request $request)
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
            0 => 'store_out_materials.id',
            1 => 'store_out_materials.id',
            2 => 'store_batch_cards.batch_card_no',
            3 => 'products.code',            
            4 => 'store_out_materials.sellable_qty',
            5 => 'store_out_materials.loss_material',
            6 => 'store_out_materials.yield',                    
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY         
        $companyId = self::_getCompanyId();
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_out_materials.id, store_out_materials.plan_id, store_out_materials.sellable_qty, store_out_materials.loss_material, store_out_materials.yield, store_productions.batch_id, store_batch_cards.batch_card_no, products.name, products.code')
        ->leftjoin('store_productions', 'store_productions.id' , '=', 'store_out_materials.plan_id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_productions.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
        ->where('store_out_materials.company_id', $companyId)
        ->where('store_productions.deleted_at', null);
        //dd($modelQuery->toSql());
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {            
           
            if (!empty($request->custom['from-date']) && !empty($request->custom['to-date'])) 
            {
                $custom_search = true;

                $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                $start_date   = date_format($dateObject, 'Y-m-d'); 

                $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                $end_date   = date_format($dateObject, 'Y-m-d'); 

                if (strtotime($start_date)==strtotime($end_date)){
                    
                    $modelQuery  = $modelQuery
                                        ->whereDate('store_batch_cards.created_at','=',$start_date);

                }else{
                    $modelQuery = $modelQuery
                                    ->whereBetween('store_batch_cards.created_at', 
                                    array($start_date,$end_date));
                }

            

            }else if(!empty($request->custom['from-date']) && empty($request->custom['to-date'])) 
            {

                $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                $start_date   = date_format($dateObject, 'Y-m-d'); 

                $modelQuery = $modelQuery
                ->whereDate('store_batch_cards.created_at','>=',$start_date);

            }else if(empty($request->custom['from-date']) && !empty($request->custom['to-date'])) 
            {

                $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                $end_date   = date_format($dateObject, 'Y-m-d'); 

                $modelQuery = $modelQuery
                ->whereDate('store_batch_cards.created_at','<=',$end_date);
            }         
            
        }

        /*if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];

                 $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('products.name', 'LIKE', '%'.$search.'%');
                    $query->orwhere('products.code', 'LIKE', '%'.$search.'%'); 
                    $query->orwhere('store_out_materials.sellable_qty', 'LIKE', '%'.$search.'%');
                    $query->orwhere('store_out_materials.loss_material', 'LIKE', '%'.$search.'%');
                    $query->orwhere('store_out_materials.yield', 'LIKE', '%'.$search.'%');
                });              

            }
        }*/

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_out_materials.id', 'DESC');
                        
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
            foreach ($object as $key => $row)
            {

                $data[$key]['id'] = $row->id;

               // $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_in_materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['batch_id']     = "<a href=".route('admin.report.showBatch',[ base64_encode(base64_encode($row->id))]).">".$row->batch_card_no.'</a>';
                $data[$key]['product_code'] =  $row->name;
                $data[$key]['sellable_qty'] =  $row->sellable_qty;
                $data[$key]['loss_material']=  number_format($row->loss_material, 2, '.', '');
                $data[$key]['yield']  =  number_format($row->yield, 2, '.', '');          

                // if($row->status==1){
                //     $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                // }elseif($row->status==0) {
                //  $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Closed</span>';
                // }
                
               //$data[$key]['actions'] =  '<div class="text-center"></div>';
                

        }
    }

    /*$objStore = new StoreBatchCardModel;
    $batchNos = $objStore->getBatchNumbers();
    $batch_no_string = '<select name="batch_id" id="batch-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_id'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';
    $objProduct = new ProductsModel;
    $products = $objProduct->getProducts($companyId);
    $product_code_string = '<select name="product_code" id="product-code" class="form-control my-select"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product_code'] == $product['id'] ? 'selected' : '').' >'.$product['code'].' ('.$product['name'].' )</option>';
        }
    $product_code_string .='</select>';
    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    $searchHTML['batch_id']     =  $batch_no_string;
    $searchHTML['product_code']     =  $product_code_string;
    $searchHTML['sellable_qty']   =  '<input type="text" class="form-control" id="sellable-qty" value="'.($request->custom['sellable_qty']).'" placeholder="Search...">';
    $searchHTML['loss_material']   =  '<input type="text" class="form-control" id="loss-material" value="'.($request->custom['loss_material']).'" placeholder="Search...">';
    $searchHTML['yield']   =  '<input type="text" class="form-control" id="yield" value="'.($request->custom['yield']).'" placeholder="Search...">';*/
    //$searchHTML['status']   =  '';  
    /*$searchHTML['status']   =  '<select name="status" id="search-status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Status</option>
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').' >Active</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').'>Closed</option>            
            </select>';
    // $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';removeSearch

    if ($custom_search) 
    {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return removeSearch(this)" class="btn btn-danger"><span class="fa  fa-remove"></span></a></div>';
    }
    else
    {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';
    }

    $searchHTML['actions'] = $seachAction;


    array_unshift($data, $searchHTML);*/

    ## WRAPPING UP
    $this->JsonData['draw']             = intval($request->draw);
    $this->JsonData['recordsTotal']     = intval($totalData);
    $this->JsonData['recordsFiltered']  = intval($totalFiltered);
    $this->JsonData['data']             = $data;

    return response()->json($this->JsonData);
}
public function agedMaterialIndex()
{
    ## DEFAULT SITE SETTINGS
    $this->ViewData['moduleTitle']  = 'Aged Material Report';
    $this->ViewData['moduleAction'] = 'Aged Material Report';
    $this->ViewData['modulePath']   = $this->ModulePath;        
    //dd('aged report');
    // view file with data
    return view($this->ModuleView.'agedMaterial',$this->ViewData);
}
public function getAgedMaterialRecords(Request $request)
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
            1 => 'store_in_materials.lot_no',
            2 => 'store_in_materials.material_id',            
            3 => 'store_in_materials.lot_balance',
            4 => 'store_in_materials.last_used_at',
            5 => 'store_in_materials.created_at',                    
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY        
        $companyId = self::_getCompanyId();        
        $model = new StoreInMaterialModel;
        $modelQuery = $model       
        ->selectRaw('store_in_materials.id, store_in_materials.material_id, store_in_materials.lot_no,store_in_materials.lot_balance,store_in_materials.last_used_at,store_in_materials.created_at, store_raw_materials.name')       
        ->leftjoin('store_raw_materials', 'store_raw_materials.id' , '=', 'store_in_materials.material_id')
        ->where('store_in_materials.company_id', $companyId)
        ->where('store_raw_materials.deleted_at', null);
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['interval-time'])) 
            {
                $custom_search = true;
                $key = $request->custom['interval-time'];
                
                /*$modelQuery = $modelQuery
                ->where('store_in_materials.created_at', '<=', 'now() - INTERVAL '.$key.' DAY');                
                $modelQuery = $modelQuery->where(function($query)use($key) {
                $query->where('last_used_at', null)
                    ->orWhere('last_used_at', '<=', 'NOW() - INTERVAL '.$key.' DAY'); 
                    });*/
                //$keyDate = '2019-11-30';
                $modelQuery = $modelQuery
                ->where('store_in_materials.created_at', '<=', Carbon::now()->subDays($key));                
                $modelQuery = $modelQuery->where(function($query)use($key) {
                    $query->where('last_used_at', null)
                    ->orWhere('last_used_at', '<=', Carbon::now()->subDays($key)); 
                });
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
            $modelQuery = $modelQuery->orderBy('store_in_materials.last_used_at', 'ASC');
                        
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get(); 
        //dd($object);
        /*--------------------------------------
        |  DATA BINDING
        ------------------------------*/

        $data = [];

        if (!empty($object) && sizeof($object) > 0)
        {
            $count =1;
            foreach ($object as $key => $row)
            {
                $last_used_at = 'Never';
                if($row->last_used_at != null)
                    $last_used_at = date('d M Y',strtotime($row->last_used_at));

                        
                $data[$key]['id'] = $row->id;
                $data[$key]['lot_no']  = $row->lot_no;
                $data[$key]['material_id']  =  $row->name;
                $data[$key]['lot_balance']  =  number_format($row->lot_balance, 2, '.', '');
                $data[$key]['last_used_at']  =  $last_used_at;
                $data[$key]['created_at']  =  date('d M Y',strtotime($row->created_at));

            }
        }    

    ## WRAPPING UP
    $this->JsonData['draw']             = intval($request->draw);
    $this->JsonData['recordsTotal']     = intval($totalData);
    $this->JsonData['recordsFiltered']  = intval($totalFiltered);
    $this->JsonData['data']             = $data;

    return response()->json($this->JsonData);
}

}
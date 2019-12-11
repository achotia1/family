<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StoreOutMaterialModel;
use App\Models\StoreBatchCardModel;
use App\Models\ProductsModel;
use App\Models\StoreInMaterialModel;
use App\Models\StoreRawMaterialModel;
use App\Models\StoreSaleInvoiceHasProductsModel;
use App\Models\StoreSaleStockModel;

use App\Traits\GeneralTrait;
use Carbon\Carbon;

class ReportController extends Controller
{   
    private $BaseModel;
    use GeneralTrait;
    
    public function __construct(

        StoreOutMaterialModel $StoreOutMaterialModel,
        StoreSaleStockModel $StoreSaleStockModel,
        StoreInMaterialModel $StoreInMaterialModel
    )
    {

        $this->BaseModel = $StoreOutMaterialModel;   
        $this->StoreSaleStockModel = $StoreSaleStockModel;   
        $this->StoreInMaterialModel = $StoreInMaterialModel;   

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
                $data[$key]['sellable_qty'] =  number_format($row->sellable_qty, 2, '.', '');
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
        //$companyId = self::_getCompanyId();
        $companyId = self::_getCompanyId();        
        $model = new StoreInMaterialModel;
        $modelQuery = $model       
        ->selectRaw('store_in_materials.id, store_in_materials.material_id, store_in_materials.lot_no,store_in_materials.lot_balance,store_in_materials.last_used_at,store_in_materials.created_at, store_raw_materials.name')       
        ->leftjoin('store_raw_materials', 'store_raw_materials.id' , '=', 'store_in_materials.material_id')
        ->where('store_in_materials.lot_balance', '>', 0)
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

## CONTRIBUTION REPORT
public function contributionIndex()
{
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Contribution Report';
        $this->ViewData['moduleAction'] = 'Contribution Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        
        
        /*$model = new StoreSaleInvoiceHasProductsModel;
        $modelQuery = $model
                        ->with(['assignedInvoice'=>function($q){
                            $q->with('hasCustomer');    
                        }])
                        ->with(['assignedProduct'])
                        ->with(['assignedBatch']);*/
        /*$companyId = 1;                
        $model = new StoreSaleInvoiceHasProductsModel;
        $modelQuery = $model
        ->selectRaw('store_sale_invoice_has_products.id, store_sale_invoice_has_products.quantity,store_sale_invoice_has_products.returned_quantity,store_sale_invoice_has_products.rate, store_sale_invoice.invoice_no, store_sale_invoice.invoice_date, users.contact_name, users.company_name, products.name, products.code, store_batch_cards.batch_card_no, store_sales_stock.manufacturing_cost')
        ->leftjoin('store_sale_invoice', 'store_sale_invoice.id' , '=', 'store_sale_invoice_has_products.sale_invoice_id')
        ->leftjoin('users', 'users.id' , '=', 'store_sale_invoice.customer_id')
        ->leftjoin('products', 'products.id' , '=', 'store_sale_invoice_has_products.product_id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_sale_invoice_has_products.batch_id')
        ->leftjoin('store_sales_stock', 'store_sales_stock.batch_id' , '=', 'store_sale_invoice_has_products.batch_id')
        ->where('store_sale_invoice.company_id', $companyId)
        ->where('store_sale_invoice.deleted_at', null);
        
        //dd($modelQuery->toSql());
        $object = $modelQuery->get();*/

        //dd($object);
        // view file with data
        return view($this->ModuleView.'contribution',$this->ViewData);
}
public function getContributionRecords(Request $request)
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
            0 => 'store_sale_invoice_has_products.id',            
            1 => 'store_sale_invoice.invoice_date',
            2 => 'store_sale_invoice.invoice_no',            
            3 => 'users.contact_name',
            4 => 'products.code',
            5 => 'store_batch_cards.batch_card_no',
            6 => 'qty',
            7 => 'store_sale_invoice_has_products.rate',
            8 => 'net',
            9 => 'store_sales_stock.manufacturing_cost',
            10 => 'gross_contibution',
            11 => 'total_contribution',
            12 => 'material_consumption',                 
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY       
        $companyId = self::_getCompanyId();
        $model = new StoreSaleInvoiceHasProductsModel;
        $modelQuery = $model
        ->selectRaw('store_sale_invoice_has_products.id, store_sale_invoice_has_products.quantity,store_sale_invoice_has_products.returned_quantity,store_sale_invoice_has_products.rate, store_sale_invoice.invoice_no, store_sale_invoice.invoice_date, users.contact_name, users.company_name, products.name, products.code, store_batch_cards.batch_card_no, store_sales_stock.manufacturing_cost, 
            store_sale_invoice_has_products.quantity - store_sale_invoice_has_products.returned_quantity as qty, ( store_sale_invoice_has_products.quantity - store_sale_invoice_has_products.returned_quantity) * rate as net, store_sale_invoice_has_products.rate - store_sales_stock.manufacturing_cost as gross_contibution, ( store_sale_invoice_has_products.quantity - store_sale_invoice_has_products.returned_quantity) * (store_sale_invoice_has_products.rate - store_sales_stock.manufacturing_cost) as total_contribution, (store_sales_stock.manufacturing_cost / store_sale_invoice_has_products.rate)*100 as material_consumption')
        ->leftjoin('store_sale_invoice', 'store_sale_invoice.id' , '=', 'store_sale_invoice_has_products.sale_invoice_id')
        ->leftjoin('users', 'users.id' , '=', 'store_sale_invoice.customer_id')
        ->leftjoin('products', 'products.id' , '=', 'store_sale_invoice_has_products.product_id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_sale_invoice_has_products.batch_id')
        ->leftjoin('store_sales_stock', 'store_sales_stock.batch_id' , '=', 'store_sale_invoice_has_products.batch_id')
        ->where('store_sale_invoice.company_id', $companyId)
        ->where('store_sale_invoice.deleted_at', null);
        
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
                                        ->whereDate('store_sale_invoice.invoice_date','=',$start_date);

                }else{
                    $modelQuery = $modelQuery
                                    ->whereBetween('store_sale_invoice.invoice_date', 
                                    array($start_date,$end_date));
                }            

            }    
            
        }

        if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];

                 $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('store_sale_invoice.invoice_no', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('users.contact_name', 'LIKE', '%'.$search.'%');
                    
                    $query->orwhere('products.code', 'LIKE', '%'.$search.'%');   
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_sale_invoice_has_products.id', 'ASC');
                        
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
            //$i = 0;
            foreach ($object as $key => $row)
            {        
                $data[$key]['id'] = $row->id;
                $data[$key]['invoice_date']  = date('d M Y',strtotime($row->invoice_date));
                $data[$key]['invoice_no']  =  $row->invoice_no;
                /*if($key > 0 && ($data[$key]['invoice_no'] == $data[$key-1]['invoice_no'])){
                    $data[$key]['invoice_no']  =  '';
                }*/
                //$data[$key]['invoice_no']  =  $row->invoice_no;
                $data[$key]['customer_name']  = $row->contact_name. " ". $row->company_name;
                $data[$key]['product_name']  =  $row->code. " (".$row->name. ")";
                $data[$key]['batch_code']  =  $row->batch_card_no;
                $data[$key]['quantity']  = number_format($row->qty,2,'.','');
                $data[$key]['rate']  =  number_format($row->rate,2,'.','');
                $data[$key]['net_cost']  =  number_format($row->net,2,'.','');
                $data[$key]['costing']  =  number_format($row->manufacturing_cost,2,'.','');
                $data[$key]['gross']  =  number_format($row->gross_contibution,2,'.','');
                $data[$key]['total']  =  number_format($row->total_contribution,2,'.','');
                $data[$key]['material_consumption']  =  number_format($row->material_consumption,2,'.','');

            }
        }    

    ## WRAPPING UP
    $this->JsonData['draw']             = intval($request->draw);
    $this->JsonData['recordsTotal']     = intval($totalData);
    $this->JsonData['recordsFiltered']  = intval($totalFiltered);
    $this->JsonData['data']             = $data;

    return response()->json($this->JsonData);
}

/*--------------------------------------
 |  Aged Product Report
------------------------------*/
    public function agedProductIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Aged Product Report';
        $this->ViewData['moduleAction'] = 'Aged Product Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        
        //dd('aged report');
        // view file with data
        return view($this->ModuleView.'agedProduct',$this->ViewData);
    }

    public function getAgedProductRecords(Request $request)
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
                0 => 'store_sales_stock.id',            
                1 => 'store_sales_stock.batch_id',
                2 => 'store_sales_stock.product_id',            
                3 => 'store_sales_stock.balance_quantity',
                4 => 'store_sales_stock.last_used_at',
                5 => 'store_sales_stock.created_at',                    
            );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

            ## START MODEL QUERY        
            //$companyId = self::_getCompanyId();
            $companyId = self::_getCompanyId();        
            // $model = new StoreInMaterialModel;
            $modelQuery = $this->StoreSaleStockModel
            ->selectRaw('store_sales_stock.id, 
                        store_sales_stock.batch_id, 
                        store_sales_stock.product_id,
                        store_sales_stock.balance_quantity,
                        store_sales_stock.last_used_at,
                        store_sales_stock.created_at, 
                        store_batch_cards.batch_card_no,
                        products.name as productName,
                        products.code as productCode
                        ')       
            ->leftjoin('products', 'products.id' , '=', 'store_sales_stock.product_id')
            ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_sales_stock.batch_id')
            ->where('store_sales_stock.balance_quantity', '>', 0)
            ->where('store_sales_stock.company_id', $companyId)
            ->where('store_batch_cards.deleted_at', null);
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
                    ->where('store_sales_stock.created_at', '<=', Carbon::now()->subDays($key));                
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
                        $query->orwhere('products.name', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('products.code', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');
                        $query->orwhere('store_sales_stock.balance_quantity', 'LIKE', '%'.$search.'%');   
                    });              

                }
            }

            ## GET TOTAL FILTER
            $filteredQuery = clone($modelQuery);            
            $totalFiltered  = $filteredQuery->count();

            ## OFFSET AND LIMIT
            if(empty($column))
            {   
                $modelQuery = $modelQuery->orderBy('store_sales_stock.last_used_at', 'ASC');
                            
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
                    $data[$key]['batch']  = $row->batch_card_no;
                    $data[$key]['product']  =  $row->productCode." (".$row->productName.")";
                    $data[$key]['stock_balance']  =  number_format($row->balance_quantity, 2, '.', '');
                    $data[$key]['last_used_at']  =  $last_used_at;
                    $data[$key]['stock_in_date']  =  date('d M Y',strtotime($row->created_at));

                }
            }    

        ## WRAPPING UP
        $this->JsonData['draw']             = intval($request->draw);
        $this->JsonData['recordsTotal']     = intval($totalData);
        $this->JsonData['recordsFiltered']  = intval($totalFiltered);
        $this->JsonData['data']             = $data;

        return response()->json($this->JsonData);
    }

/*--------------------------------------
 |  Deviation Report
------------------------------*/

    public function deviationMaterialIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Deviation Material Report';
        $this->ViewData['moduleAction'] = 'Deviation Material Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        
        //dd('aged report');
        // view file with data
        return view($this->ModuleView.'deviationMaterial',$this->ViewData);
    }

    public function getdeviationMaterialRecords(Request $request)
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
                3 => 'store_in_materials.balance_corrected_at',
            );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

            ## START MODEL QUERY        
            $companyId = self::_getCompanyId();        

            $modelQuery = $this->StoreInMaterialModel
            ->selectRaw('store_in_materials.id, 
                        store_in_materials.lot_no, 
                        store_in_materials.material_id,
                        store_in_materials.balance_corrected_at,
                        store_in_materials.created_at, 
                        store_raw_materials.name as materialName
                        ')       
            ->leftjoin('store_raw_materials', 'store_raw_materials.id' , '=', 'store_in_materials.material_id')
            ->where('store_in_materials.company_id', $companyId)
            ->whereNotNull('store_in_materials.balance_corrected_at')
            ->where('store_raw_materials.deleted_at', null);
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
                                            ->whereDate('store_in_materials.balance_corrected_at','=',$start_date);

                    }else{
                        $modelQuery = $modelQuery
                                        ->whereBetween('store_in_materials.balance_corrected_at', 
                                        array($start_date,$end_date));
                    }

                

                }else if(!empty($request->custom['from-date']) && empty($request->custom['to-date'])) 
                {

                    $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                    $start_date   = date_format($dateObject, 'Y-m-d'); 

                    $modelQuery = $modelQuery
                    ->whereDate('store_in_materials.balance_corrected_at','>=',$start_date);

                }else if(empty($request->custom['from-date']) && !empty($request->custom['to-date'])) 
                {

                    $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                    $end_date   = date_format($dateObject, 'Y-m-d'); 

                    $modelQuery = $modelQuery
                    ->whereDate('store_in_materials.balance_corrected_at','<=',$end_date);
                }         
                
            }
           
            //Datatable Global Search
            if (!empty($request->search))
            {
                if (!empty($request->search['value'])) 
                {
                    $search = $request->search['value'];

                     $modelQuery = $modelQuery->where(function ($query) use($search)
                    {
                        $query->orwhere('store_in_materials.lot_no', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('store_raw_materials.name', 'LIKE', '%'.$search.'%');   
                    });              

                }
            }

            ## GET TOTAL FILTER
            $filteredQuery = clone($modelQuery);            
            $totalFiltered  = $filteredQuery->count();

            ## OFFSET AND LIMIT
            if(empty($column))
            {   
                $modelQuery = $modelQuery->orderBy('store_in_materials.balance_corrected_at', 'ASC');
                            
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
                    $balance_corrected_at = 'Never';
                    if($row->balance_corrected_at != null)
                        $balance_corrected_at = date('d M Y',strtotime($row->balance_corrected_at));
                            
                    $data[$key]['id'] = $row->id;
                    $data[$key]['lot_no']  = $row->lot_no;
                    $data[$key]['materialName']  =  $row->materialName;
                    $data[$key]['balance_corrected_at']  =  $balance_corrected_at;
                    // $data[$key]['stock_in_date']  =  date('d M Y',strtotime($row->created_at));

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
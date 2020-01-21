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
use App\Models\StoreLotCorrectionModel;
use App\Models\StoreStockCorrectionModel;
use App\Models\StoreTempRawMaterialModel;
use App\Models\StoreTempAvgYieldModel;


use App\Traits\GeneralTrait;
use Carbon\Carbon;
use DB;
class ReportController extends Controller
{   
    private $BaseModel;
    use GeneralTrait;
    
    public function __construct(

        StoreOutMaterialModel $StoreOutMaterialModel,
        StoreSaleStockModel $StoreSaleStockModel,
        StoreInMaterialModel $StoreInMaterialModel,
        StoreLotCorrectionModel $StoreLotCorrectionModel,
        StoreRawMaterialModel $StoreRawMaterialModel
    )
    {

        $this->BaseModel = $StoreOutMaterialModel;   
        $this->StoreSaleStockModel = $StoreSaleStockModel;   
        $this->StoreInMaterialModel = $StoreInMaterialModel;   
        $this->StoreLotCorrectionModel = $StoreLotCorrectionModel;
        $this->StoreRawMaterialModel = $StoreRawMaterialModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Reports';
        $this->ModuleView  = 'admin.reports.';
        $this->ModulePath  = 'admin.reports.';

        $this->middleware(['permission:store-batch-wise-report'], ['only' => ['batchIndex','getBatchRecords']]);
        $this->middleware(['permission:store-aged-material-report'], ['only' => ['agedMaterialIndex','getAgedMaterialRecords']]);
        $this->middleware(['permission:store-raw-material-report'], ['only' => ['rawMaterialIndex','getRawMaterialRecords']]);
        $this->middleware(['permission:store-material-deviation-report'], ['only' => ['deviationMaterialIndex','getdeviationMaterialRecords','deviationLotHistoryIndex','getdeviationLotHistoryRecords']]);
        $this->middleware(['permission:store-contribution-report'], ['only' => ['contributionIndex','getContributionRecords']]);
        $this->middleware(['permission:store-aged-product-report'], ['only' => ['agedProductIndex','getAgedProductRecords']]);
        $this->middleware(['permission:store-stock-deviation-report'], ['only' => ['deviationStockIndex','getdeviationStockRecords']]);
        $this->middleware(['permission:store-avg-yield-report'], ['only' => ['avgYieldIndex','getAvgYieldRecords']]);
        
    }

/*--------------------------------------
 |  Batch-Wise Report
------------------------------*/
    public function batchIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Batch-Wise Report';
        $this->ViewData['moduleAction'] = 'Batch-Wise Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        
        $companyId = self::_getCompanyId();     
        $objProduct = new ProductsModel;
        $products = $objProduct->getProducts($companyId);
           
        $this->ViewData['products']   = $products;
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
                1 => 'store_batch_cards.batch_card_no',
                2 => 'products.code',
                3 => 'store_out_materials.sellable_qty',            
                4 => 'store_out_materials.loss_material',
                5 => 'store_out_materials.yield',                                   
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
                        // $modelQuery = $modelQuery
                        //                 ->whereBetween('store_batch_cards.created_at', 
                        //                 array($start_date,$end_date));

                        $modelQuery = $modelQuery
                                        ->whereDate('store_batch_cards.created_at','>=',$start_date)
                                        ->whereDate('store_batch_cards.created_at','<=',$end_date);
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
                if (!empty($request->custom['product-id'])) 
                {
                    $custom_search = true;
                    $product_id = $request->custom['product-id'];
                    
                    $modelQuery = $modelQuery
                                        ->where('products.id',$product_id);

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
                        $query->orwhere('products.name', 'LIKE', '%'.$search.'%');
                        $query->orwhere('products.code', 'LIKE', '%'.$search.'%'); 
                        $query->orwhere('store_out_materials.sellable_qty', 'LIKE', '%'.$search.'%');
                        $query->orwhere('store_out_materials.loss_material', 'LIKE', '%'.$search.'%');
                        $query->orwhere('store_out_materials.yield', 'LIKE', '%'.$search.'%');
                    });              

                }
            }

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

                    $data[$key]['batch_id']     = "<a class='cls-details' href=".route('admin.report.showBatch',[ base64_encode(base64_encode($row->id))]).">".$row->batch_card_no.'</a>';
                    /*$data[$key]['batch_id']     = "<a class='cls-details'>".$row->batch_card_no.'</a>';*/
                    $data[$key]['product_code'] =  $row->code." (".$row->name.")";
                    $data[$key]['sellable_qty'] =  number_format($row->sellable_qty, 2, '.', '');
                    $data[$key]['loss_material']=  number_format($row->loss_material, 2, '.', '');
                    $data[$key]['yield']  =  number_format($row->yield, 2, '.', '');
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
 |  Aged Material REPORT
------------------------------*/    

    public function agedMaterialIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Aged Material Report';
        $this->ViewData['moduleAction'] = 'Aged Material Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        
        $companyId = self::_getCompanyId();

        $objMaterial = new StoreRawMaterialModel;
        $materials = $objMaterial->getMaterialNumbers($companyId);
        //dd($materials);
        $this->ViewData['materials']   = $materials; 
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
                2 => 'store_raw_materials.name',            
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
                $modelQuery = $modelQuery
                ->where('store_in_materials.created_at', '<=', Carbon::now()->subDays($key));                
                $modelQuery = $modelQuery->where(function($query)use($key) {
                    $query->where('last_used_at', null)
                    ->orWhere('last_used_at', '<=', Carbon::now()->subDays($key)); 
                });
            }
            if (!empty($request->custom['material-id'])) 
            {
                $custom_search = true;
                $material_id = $request->custom['material-id'];
                
                $modelQuery = $modelQuery
                            ->where('store_in_materials.material_id',$material_id);

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


/*--------------------------------------
 |  CONTRIBUTION REPORT
------------------------------*/    
    public function contributionIndex()
    {
            ## DEFAULT SITE SETTINGS
            $this->ViewData['moduleTitle']  = 'Contribution Report';
            $this->ViewData['moduleAction'] = 'Contribution Report';
            $this->ViewData['modulePath']   = $this->ModulePath;        
            
            $companyId = self::_getCompanyId();     
            $objProduct = new ProductsModel;
            $products = $objProduct->getProducts($companyId);
           
            $this->ViewData['products']   = $products;

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
                        // $modelQuery = $modelQuery
                        //                 ->whereBetween('store_sale_invoice.invoice_date', 
                        //                 array($start_date,$end_date));
                        $modelQuery  = $modelQuery
                                            ->whereDate('store_sale_invoice.invoice_date','>=',$start_date)
                                            ->whereDate('store_sale_invoice.invoice_date','<=',$end_date);
                    }            

                } else if(!empty($request->custom['from-date']) && empty($request->custom['to-date'])) 
                {

                    $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                    $start_date   = date_format($dateObject, 'Y-m-d'); 

                    $modelQuery = $modelQuery
                    ->whereDate('store_sale_invoice.invoice_date','>=',$start_date);

                }else if(empty($request->custom['from-date']) && !empty($request->custom['to-date'])) 
                {

                    $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                    $end_date   = date_format($dateObject, 'Y-m-d'); 

                    $modelQuery = $modelQuery
                    ->whereDate('store_sale_invoice.invoice_date','<=',$end_date);
                }

                if (!empty($request->custom['product-id'])) 
                {
                    $custom_search = true;
                    $product_id = $request->custom['product-id'];
                    
                    $modelQuery = $modelQuery
                                        ->where('store_sale_invoice_has_products.product_id',$product_id);

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
        
        $companyId = self::_getCompanyId();     
        $objProduct = new ProductsModel;
        $products = $objProduct->getProducts($companyId);           
        $this->ViewData['products']   = $products;

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
                1 => 'store_batch_cards.batch_card_no',
                2 => 'products.code',            
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

            //dd($request->custom);
            ## FILTER OPTIONS
            $custom_search = false;
            if (!empty($request->custom))
            {
                if (!empty($request->custom['interval-time'])) 
                {
                    $custom_search = true;
                    $key = $request->custom['interval-time'];
                                       
                    $modelQuery = $modelQuery
                    ->where('store_sales_stock.created_at', '<=', Carbon::now()->subDays($key));                
                    $modelQuery = $modelQuery->where(function($query)use($key) {
                        $query->where('last_used_at', null)
                        ->orWhere('last_used_at', '<=', Carbon::now()->subDays($key)); 
                    });
                }
                if (!empty($request->custom['product-id'])) 
                {
                    $custom_search = true;
                    $product_id = $request->custom['product-id'];
                    
                    $modelQuery = $modelQuery
                                        ->where('products.id',$product_id);

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
        $this->ViewData['moduleTitle']  = 'Material Deviation Report';
        $this->ViewData['moduleAction'] = 'Material Deviation Report';
        $this->ViewData['modulePath']   = $this->ModulePath;   

        $companyId = self::_getCompanyId();
        $materials = $this->StoreRawMaterialModel
                            ->join('store_in_materials', 'store_in_materials.material_id' , '=', 'store_raw_materials.id')
                            ->where('store_raw_materials.company_id', $companyId)
                            ->whereNotNull('store_in_materials.balance_corrected_at')
                            ->groupBy('store_raw_materials.id')
                            ->get([
                                'store_raw_materials.id',
                                'store_raw_materials.name'
                                ]);

        $this->ViewData['materials']   = $materials;    

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
                2 => 'store_raw_materials.name',            
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
                        // $modelQuery = $modelQuery
                        //                 ->whereBetween('store_in_materials.balance_corrected_at', 
                        //                 array($start_date,$end_date));
                        $modelQuery  = $modelQuery
                                            ->whereDate('store_in_materials.balance_corrected_at','>=',$start_date)
                                            ->whereDate('store_in_materials.balance_corrected_at','<=',$end_date);
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

                if (!empty($request->custom['material-id'])) 
                {
                    $custom_search = true;
                    $material_id = $request->custom['material-id'];
                    
                    $modelQuery = $modelQuery
                                        ->where('store_raw_materials.id',$material_id);

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
                    $data[$key]['lot_no']     = "<a href=".route('admin.report.deviationLotHistory',[ base64_encode(base64_encode($row->id))]).">". $row->lot_no.'</a>';
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


/*--------------------------------------
 |  Deviation Lot History Report
------------------------------*/

    public function deviationLotHistoryIndex($encID)
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['modulePath']   = $this->ModulePath;  

        $material = $this->StoreRawMaterialModel
                            ->join('store_in_materials', 'store_in_materials.material_id' , '=', 'store_raw_materials.id')
                            //->where('store_raw_materials.company_id', $companyId)
                            //->whereNotNull('store_in_materials.balance_corrected_at')
                            ->where('store_in_materials.id', base64_decode(base64_decode($encID)))
                            ->first([
                                'store_raw_materials.id',
                                'store_raw_materials.name'
                                ]);  
       // dd($materials)   ;

        $this->ViewData['lotId']  = $encID;             
        $this->ViewData['moduleTitle']  = 'Deviation History';
        $this->ViewData['moduleAction'] = 'Deviation History of Material:'.$material->name;
        // $this->ViewData['material']  = $material;             

        // view file with data
        return view($this->ModuleView.'deviationLotHistory',$this->ViewData);
    }

    public function getdeviationLotHistoryRecords(Request $request,$encID)
    {

        /*--------------------------------------
        |  VARIABLES
        ------------------------------*/

            $lotId  = base64_decode(base64_decode($encID));             

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
                2 => 'store_lot_corrections.previous_balance',            
                3 => 'store_lot_corrections.corrected_balance',
                4 => 'store_lot_corrections.correction_date',
            );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

            ## START MODEL QUERY        
            $companyId = self::_getCompanyId();        

            $modelQuery = $this->StoreLotCorrectionModel
            ->selectRaw('store_lot_corrections.id, 
                        store_in_materials.lot_no, 
                        store_lot_corrections.previous_balance,
                        store_lot_corrections.corrected_balance,
                        store_lot_corrections.correction_date
                        ')       
            ->join('store_in_materials', 'store_in_materials.id' , '=', 'store_lot_corrections.lot_id')
            ->where('store_in_materials.company_id', $companyId)
            ->where('store_in_materials.id', $lotId)
            ->whereNull('store_in_materials.deleted_at');
            ## GET TOTAL COUNT
            $countQuery = clone($modelQuery);            
            $totalData  = $countQuery->count();

            ## FILTER OPTIONS
            $custom_search = false;
            
            //Datatable Global Search
            if (!empty($request->search))
            {
                if (!empty($request->search['value'])) 
                {
                    $search = $request->search['value'];

                     $modelQuery = $modelQuery->where(function ($query) use($search)
                    {
                        $query->orwhere('store_in_materials.lot_no', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('store_lot_corrections.previous_balance', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('store_lot_corrections.corrected_balance', 'LIKE', '%'.$search.'%');   
                    });              

                }
            }

            ## GET TOTAL FILTER
            $filteredQuery = clone($modelQuery);            
            $totalFiltered  = $filteredQuery->count();

            ## OFFSET AND LIMIT
            if(empty($column))
            {   
                $modelQuery = $modelQuery->orderBy('store_lot_corrections.correction_date', 'ASC');
                            
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
                foreach ($object as $key => $row)
                {
                    $data[$key]['id']               =   $row->id;
                    $data[$key]['lot_no']           =   $row->lot_no;
                    $data[$key]['previous_balance']  =  $row->previous_balance;
                    $data[$key]['corrected_balance'] =  $row->corrected_balance;
                    $data[$key]['correction_date']   =   date('d M Y',strtotime($row->correction_date));

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
 |  Stock Deviation Report
------------------------------*/

    public function deviationStockIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Stock Deviation Report';
        $this->ViewData['moduleAction'] = 'Stock Deviation Report';
        $this->ViewData['modulePath']   = $this->ModulePath;   

        $companyId = self::_getCompanyId();     
        $objProduct = new ProductsModel;
        $products = $objProduct->getDeviatedProducts($companyId);
       
        $this->ViewData['products']   = $products;
        // view file with data
        return view($this->ModuleView.'deviationStock',$this->ViewData);
    }
    public function getdeviationStockRecords(Request $request)
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
                1 => 'store_batch_cards.batch_card_no',
                2 => 'products.code',            
                3 => 'store_sales_stock.balance_corrected_at',
            );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

            ## START MODEL QUERY        
            $companyId = self::_getCompanyId();        

            $modelQuery = $this->StoreSaleStockModel
            ->selectRaw('store_sales_stock.id, 
                        store_sales_stock.batch_id, 
                        store_sales_stock.product_id,
                        store_sales_stock.balance_corrected_at,
                        store_sales_stock.created_at, 
                        store_batch_cards.batch_card_no,
                        products.name,
                        products.code
                        ')       
            ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_sales_stock.batch_id')
            ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
            ->where('store_sales_stock.company_id', $companyId)
            ->whereNotNull('store_sales_stock.balance_corrected_at')
            ->where('store_batch_cards.deleted_at', null);
            
            ## GET TOTAL COUNT
            $countQuery = clone($modelQuery);            
            $totalData  = $countQuery->count();
            //dd($request->all());
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
                                            ->whereDate('store_sales_stock.balance_corrected_at','=',$start_date);

                    }else{
                        // $modelQuery = $modelQuery
                        //                 ->whereBetween('store_sales_stock.balance_corrected_at', 
                        //                 array($start_date,$end_date));
                         $modelQuery  = $modelQuery
                                            ->whereDate('store_sales_stock.balance_corrected_at','>=',$start_date)
                                            ->whereDate('store_sales_stock.balance_corrected_at','<=',$end_date);
                    }

                

                }else if(!empty($request->custom['from-date']) && empty($request->custom['to-date'])) 
                {

                    $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                    $start_date   = date_format($dateObject, 'Y-m-d'); 

                    $modelQuery = $modelQuery
                    ->whereDate('store_sales_stock.balance_corrected_at','>=',$start_date);

                }else if(empty($request->custom['from-date']) && !empty($request->custom['to-date'])) 
                {

                    $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                    $end_date   = date_format($dateObject, 'Y-m-d'); 

                    $modelQuery = $modelQuery
                    ->whereDate('store_sales_stock.balance_corrected_at','<=',$end_date);
                } 

                if (!empty($request->custom['product-id'])) 
                {
                    $custom_search = true;
                    $product_id = $request->custom['product-id'];
                    
                    $modelQuery = $modelQuery
                                        ->where('products.id',$product_id);

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
                        $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('products.name', 'LIKE', '%'.$search.'%');
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
                $modelQuery = $modelQuery->orderBy('store_sales_stock.balance_corrected_at', 'ASC');
                            
            }
            else
            {
                $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
            }
            
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
                    $data[$key]['batch_code']  = "<a href=".route('admin.report.deviationStockHistory',[ base64_encode(base64_encode($row->id))]).">". $row->batch_card_no.'</a>'; //
                    $data[$key]['product_code']  =  $row->code." ( ".$row->name." )";
                    $data[$key]['balance_corrected_at']  =  $balance_corrected_at;

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
 |  Deviation STOCK History Report
------------------------------*/

    public function deviationStockHistoryIndex($encID)
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['modulePath']   = $this->ModulePath;  

         
        $objProduct = new ProductsModel;
        $product = $objProduct
                    ->join('store_sales_stock','store_sales_stock.product_id', '=', 'products.id')
                    ->where('store_sales_stock.id', base64_decode(base64_decode($encID)))
                    ->first(['product_id','name','code']);
        //dd($products)   ;

        $this->ViewData['stockId']  = $encID;             
        $this->ViewData['moduleTitle']  = 'Deviation History';
        $this->ViewData['moduleAction'] = 'Deviation History of Product:'.$product->code." (".$product->name.")";
                
        // view file with data
        return view($this->ModuleView.'deviationStockHistory',$this->ViewData);
    }

    public function getdeviationStockHistoryRecords(Request $request,$encID)
    {

        /*--------------------------------------
        |  VARIABLES
        ------------------------------*/

            $stockId  = base64_decode(base64_decode($encID));             

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
                0 => 'store_stock_corrections.id',            
                1 => 'store_batch_cards.batch_card_no',
                2 => 'store_stock_corrections.previous_balance',            
                3 => 'store_stock_corrections.corrected_balance',
                4 => 'store_stock_corrections.correction_date',
            );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

            ## START MODEL QUERY        
            $companyId = self::_getCompanyId();        
            $objStockCorrection = new StoreStockCorrectionModel;
            $modelQuery = $objStockCorrection
            ->selectRaw('store_stock_corrections.id, 
                        store_batch_cards.batch_card_no, 
                        store_stock_corrections.previous_balance,
                        store_stock_corrections.corrected_balance,
                        store_stock_corrections.correction_date
                        ')       
            ->leftjoin('store_sales_stock', 'store_sales_stock.id' , '=', 'store_stock_corrections.stock_id')
            ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_sales_stock.batch_id')
            ->where('store_sales_stock.company_id', $companyId)
            ->where('store_sales_stock.id', $stockId)
            ->whereNull('store_sales_stock.deleted_at');
            //dd($modelQuery->toSql());
            ## GET TOTAL COUNT
            $countQuery = clone($modelQuery);            
            $totalData  = $countQuery->count();

            ## FILTER OPTIONS
            $custom_search = false;
            
            //Datatable Global Search
            if (!empty($request->search))
            {
                if (!empty($request->search['value'])) 
                {
                    $search = $request->search['value'];

                     $modelQuery = $modelQuery->where(function ($query) use($search)
                    {
                        $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('store_stock_corrections.previous_balance', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('store_stock_corrections.corrected_balance', 'LIKE', '%'.$search.'%');   
                    });              

                }
            }

            ## GET TOTAL FILTER
            $filteredQuery = clone($modelQuery);            
            $totalFiltered  = $filteredQuery->count();

            ## OFFSET AND LIMIT
            if(empty($column))
            {   
                $modelQuery = $modelQuery->orderBy('store_stock_corrections.correction_date', 'ASC');
                            
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
                foreach ($object as $key => $row)
                {
                    $data[$key]['id']               =   $row->id;
                    $data[$key]['batch_code']           =   $row->batch_card_no;
                    $data[$key]['previous_balance']  =  $row->previous_balance;
                    $data[$key]['corrected_balance'] =  $row->corrected_balance;
                    $data[$key]['correction_date']   =   date('d M Y',strtotime($row->correction_date));

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
 |  Raw Material REPORT
------------------------------*/    

    public function rawMaterialIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Raw Material Report';
        $this->ViewData['moduleAction'] = 'Raw Material Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        
        $companyId = self::_getCompanyId();

        $objMaterial = new StoreRawMaterialModel;
        $materials = $objMaterial->getMaterialNumbers($companyId);

        //dd($materials);
        $this->ViewData['materials']   = $materials; 
        // view file with data
        return view($this->ModuleView.'rawMaterials',$this->ViewData);
    }
    public function getRawMaterialRecords(Request $request)
    {
        
        
        //dd($balResult);
        /* TRUNCATE AND ADD NEW DATA IN TEMP TABLE */        
        $start_date = $end_date   = date('Y-m-d');        
        if (!empty($request->custom)){
            if (!empty($request->custom['from-date']) && !empty($request->custom['to-date'])) {
                $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                $start_date   = date_format($dateObject, 'Y-m-d'); 

                $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                $end_date   = date_format($dateObject, 'Y-m-d');

            }
        }
        //dd($start_date);
        $companyId = self::_getCompanyId();
        $tempCollection = new StoreTempRawMaterialModel;
        $tempCollection->query()->truncate();
        
        $objMaterial = new StoreRawMaterialModel;
        $receivedSub= DB::raw('(SELECT deleted_at, material_id,
                    SUM(CASE 
                        WHEN store_in_materials.status = 1 
                        THEN store_in_materials.lot_qty 
                        ELSE 0 
                    END) AS received_total
                    FROM store_in_materials
                    WHERE deleted_at is null
                    and DATE_FORMAT(`store_in_materials`.`created_at`, "%Y-%m-%d") >= "'.$start_date.'" 
                    and DATE_FORMAT(`store_in_materials`.`created_at`, "%Y-%m-%d") <= "'.$end_date.'" 
                    GROUP BY material_id) im');
      
        /*$issuedSub = DB::raw('(SELECT material_id, 
                SUM(store_production_has_materials.quantity) as total_issued,
                SUM(store_production_has_materials.returned_quantity) as total_returned
                FROM store_production_has_materials
                where DATE_FORMAT(`store_production_has_materials`.`created_at`, "%Y-%m-%d") >= "'.$start_date.'" 
                and date(`store_production_has_materials`.`created_at`) <= "'.$end_date.'" 
                GROUP BY material_id) hm');*/
        $issuedSub = DB::raw('(SELECT material_id, 
                SUM(store_production_has_materials.quantity) as total_issued
                FROM store_production_has_materials
                where DATE_FORMAT(`store_production_has_materials`.`created_at`, "%Y-%m-%d") >= "'.$start_date.'" 
                and date(`store_production_has_materials`.`created_at`) <= "'.$end_date.'" 
                GROUP BY material_id) hm');

        $returnedSub = DB::raw('(SELECT material_id,                
                SUM(store_returned_materials_has_materials.quantity) as total_returned
                FROM store_returned_materials_has_materials
                where DATE_FORMAT(`store_returned_materials_has_materials`.`created_at`, "%Y-%m-%d") >= "'.$start_date.'" 
                and date(`store_returned_materials_has_materials`.`created_at`) <= "'.$end_date.'" 
                GROUP BY material_id) hrm');

        /*$opBalSub = DB::raw('(SELECT material_id, 
                    SUM(CASE 
                        WHEN store_in_materials.status = 0 
                        THEN store_in_materials.lot_qty 
                        ELSE store_in_materials.lot_balance
                    END) AS opening_balance
                    FROM `store_in_materials` 
            WHERE DATE_FORMAT(created_at, "%Y-%m-%d") < "'.$start_date.'"
            GROUP BY material_id) im');*/

        

        $modelQuery = $objMaterial
       ->selectRaw('
                    store_raw_materials.id,
                    store_raw_materials.name,
                    store_raw_materials.moq,
                    store_raw_materials.unit, 
                    store_raw_materials.material_type,
                    store_raw_materials.status,
                    IFNULL(im.received_total, 0) AS received_total,
                    IFNULL(hm.total_issued, 0) AS total_issued,
                    IFNULL(hrm.total_returned, 0) AS total_returned
                ')        
        ->leftjoin($receivedSub,function($join){
            $join->on('im.material_id','=','store_raw_materials.id');
        })
        ->leftjoin($issuedSub,function($join){
            $join->on('hm.material_id','=','store_raw_materials.id');
        })
        ->leftjoin($returnedSub,function($join){
            $join->on('hrm.material_id','=','store_raw_materials.id');
        })
        ->where('store_raw_materials.company_id', $companyId);
       
        /*$modelQuery2 = $objMaterial
       ->selectRaw('
                    store_raw_materials.id,
                    IFNULL(im.opening_balance, 0) AS opening_balance
                ')        
        ->leftjoin($opBalSub,function($join){
            $join->on('im.material_id','=','store_raw_materials.id');
        })        
        ->where('store_raw_materials.company_id', $companyId);*/        
        
        //dd($modelQuery2->toSql());

        $insertArray = array();
        $result = $modelQuery->get();
        //dd($result);
        //$result2 = $modelQuery2->get();
        if($result){            
            foreach($result as $key=>$val){
                $insertArray[$val->id]['material_id'] = $val->id;
                $insertArray[$val->id]['name'] = $val->name;
                $insertArray[$val->id]['moq'] = $val->moq;
                $insertArray[$val->id]['unit'] = $val->unit;
                $insertArray[$val->id]['material_type'] = $val->material_type;
                $insertArray[$val->id]['received_qty'] = $val->received_total;
                $insertArray[$val->id]['issued_qty'] = $val->total_issued;
                $insertArray[$val->id]['returned_qty'] = $val->total_returned;
            }
            //$tempCollection->
        }
        //dd($result2);
        /*if($result2){            
            foreach($result2 as $key2=>$val2){
                $insertArray[$val2->id]['opening_balance'] = $val2->opening_balance;
            }            
        }*/
        //$openingDate =  Carbon::today()->format('Y-m-d');
        $openingDate = $start_date;
        $mObj = new StoreRawMaterialModel;
        $openingData = $mObj
        ->with([   
            'hasOpeningMaterials' => function($q) use ($openingDate)
            {  
                $q->where('opening_date', $openingDate);                
            }
            ])
        ->where('company_id', $companyId)
        ->get()->toArray();
        //dd($openingData);
        //$balResult = array();
        if(!empty($openingData)){
            foreach($openingData as $oKey=>$oVal){                
                if(isset($oVal['has_opening_materials'][0]['opening_bal']))
                    $insertArray[$oVal['id']]['opening_balance'] = $oVal['has_opening_materials'][0]['opening_bal'];
                else
                     $insertArray[$oVal['id']]['opening_balance'] = 0;              
            }
        }

        if($insertArray){            
            foreach($insertArray as $key1=>$val1){
                $insertArray[$val1['material_id']]['balance_qty'] = ((float)$val1['opening_balance'] + (float)$val1['received_qty'] + (float)$val1['returned_qty']) - (float)$val1['issued_qty'] ;
            }

            $tempCollection->insert($insertArray);           
        }
        /* END TRUNCATE AND ADD NEW DATA IN TEMP TABLE */
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
            0 => 'store_temp_raw_materials.id',
            1 => 'store_temp_raw_materials.name',
            2 => 'store_temp_raw_materials.unit',
            3 => 'store_temp_raw_materials.opening_balance',     
            4 => 'store_temp_raw_materials.received_qty',
            5 => 'store_temp_raw_materials.issued_qty',
            6 => 'store_temp_raw_materials.returned_qty',
            7 => 'store_temp_raw_materials.balance_qty',
            8 => 'store_temp_raw_materials.moq',               
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY       
               
       
        $objTemp = new StoreTempRawMaterialModel;
        $modelQuery =  $objTemp;
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['material-id'])) 
            {
                $custom_search = true;
                $material_id = $request->custom['material-id'];
                
                $modelQuery = $modelQuery
                            ->where('store_temp_raw_materials.material_id',$material_id);

            }
        }

        if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];

                $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('name', 'LIKE', '%'.$search.'%');
                    $query->orwhere('moq', 'LIKE', '%'.$search.'%');
                    $query->orwhere('unit', 'LIKE', '%'.$search.'%');
                    $query->orwhere('balance_qty', '=', $search);
                    $query->orwhere('opening_balance', '=', $search);
                    $query->orwhere('received_qty', '=', $search);
                    $query->orwhere('issued_qty', '=', $search);
                    $query->orwhere('returned_qty', '=', $search); 
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_temp_raw_materials.name', 'ASC');
                        
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

                $order_status = '-';
                    if($row->moq >= $row->balance_qty)
                        $order_status = 'ORDER';

                $data[$key]['id'] = $row->id;
                $data[$key]['name']  = '<span title="'.ucfirst($row->name).'">'.str_limit(ucfirst($row->name), '60', '...').'</span> ';
                $data[$key]['units']  = $row->unit;
                $data[$key]['opening_stock']  = !empty($row->opening_balance) ? number_format($row->opening_balance, 2, '.', ''): '0.00';
                $data[$key]['received_qty']  = !empty($row->received_qty) ? number_format($row->received_qty, 2, '.', ''): '0.00';
                $data[$key]['issued_qty']  = !empty($row->issued_qty) ? number_format($row->issued_qty, 2, '.', ''): '0.00';
                $data[$key]['return_qty']  = !empty($row->returned_qty) ? number_format($row->returned_qty, 2, '.', ''): '0.00';
                $data[$key]['balance_qty']  =  !empty($row->balance_qty) ? number_format($row->balance_qty, 2, '.', ''): '0.00';               
                $data[$key]['moq']  =  number_format($row->moq, 2, '.', '');
                $data[$key]['status'] = $order_status;

         }
     }

    ## WRAPPING UP
    $this->JsonData['draw']             = intval($request->draw);
    $this->JsonData['recordsTotal']     = intval($totalData);
    $this->JsonData['recordsFiltered']  = intval($totalFiltered);
    $this->JsonData['data']             = $data;

    return response()->json($this->JsonData);
}
    public function avgYieldIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Average Yield Report';
        $this->ViewData['moduleAction'] = 'Average Yield Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        
        $companyId = self::_getCompanyId();     
        $objProduct = new ProductsModel;
        $products = $objProduct->getProducts($companyId);
           
        $this->ViewData['products']   = $products;
        // view file with data
        return view($this->ModuleView.'avgYield',$this->ViewData);
    }
    public function getAvgYieldRecords(Request $request)
    {

        /* TRUNCATE AND ADD NEW DATA IN TEMP TABLE */
        $start_date = $end_date   = date('Y-m-d');
        if (!empty($request->custom)){
            if (!empty($request->custom['from-date']) && !empty($request->custom['to-date'])) {
                $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                $start_date   = date_format($dateObject, 'Y-m-d'); 

                $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                $end_date   = date_format($dateObject, 'Y-m-d');

            }
        }
        $tempCollection = new StoreTempAvgYieldModel;
        $tempCollection->query()->truncate();

        $companyId = self::_getCompanyId();
        $objBatchCard = new StoreBatchCardModel;
        //$start_date = '2020-01-01';
        //$end_date = '2020-01-20';
        $modelQuery1 =  $objBatchCard        
            ->selectRaw('store_batch_cards.id,
                        store_batch_cards.batch_card_no,
                        store_batch_cards.product_code,
                        products.name,
                        products.code,
                        store_productions.id as pid,
                        store_out_materials.id as out_id,
                        store_out_materials.sellable_qty,
                        store_out_materials.yield')
            ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
            ->leftjoin('store_productions', 'store_productions.batch_id' , '=', 'store_batch_cards.id')
            ->leftjoin('store_out_materials', 'store_out_materials.plan_id' , '=', 'store_productions.id')
            ->where('store_batch_cards.review_status', 'closed')
            ->whereDate('store_batch_cards.created_at','>=',$start_date)
            ->whereDate('store_batch_cards.created_at','<=',$end_date)
            ->where('store_batch_cards.company_id', $companyId)
            ->where('store_productions.deleted_at', null)
            ->where('store_out_materials.deleted_at', null)
            ->orderBy('store_batch_cards.id', 'DESC');

        $insertArray = array();
        $result = $modelQuery1->get();
        if($result){            
            foreach($result as $key=>$val){
                $insertArray[$val->id]['batch_id'] = $val->id;
                $insertArray[$val->id]['batch_card_no'] = $val->batch_card_no;
                $insertArray[$val->id]['product_id'] = $val->product_code;
                $insertArray[$val->id]['product'] = $val->code.' ('. $val->name. ')';
                //$insertArray[$val->id]['pid'] = $val->pid;
                $insertArray[$val->id]['out_id'] = $val->out_id;
                $insertArray[$val->id]['sellable_qty'] = $val->sellable_qty;
                $insertArray[$val->id]['yield'] = $val->yield;

                ##
                $objOutput = new StoreOutMaterialModel;
                $outputDetails = $objOutput->with([                
                'assignedPlan' => function($q)
                {  
                    $q->with(['hasProductionMaterials' => function($q){
                        $q->with('mateialName');
                         
                    }]);
                    $q->with(['hasReturnMaterial' => function($q){
                        $q->with('hasReturnedMaterials');
                    }]);   
                }
                ])->find($val->out_id);
                $finalTotal = 0;                
                if(!empty($outputDetails->assignedPlan->hasProductionMaterials)){
                    foreach ($outputDetails->assignedPlan->hasProductionMaterials as $key => $material) {
                        if($material->mateialName->material_type == 'Raw'){
                            $returned = 0;
                            
                            if(isset($outputDetails->assignedPlan->hasReturnMaterial->hasReturnedMaterials))
                            {
                                foreach($outputDetails->assignedPlan->hasReturnMaterial->hasReturnedMaterials as $returnedMaterial){
                                    if( $material->lot_id == $returnedMaterial->lot_id)
                                        $returned = $returnedMaterial->quantity;
                                }
                            }
                            $finalWeight = $material->quantity - $returned;
                            $finalTotal = $finalTotal + $finalWeight;
                        }
                    }
                }                
                $insertArray[$val->id]['input_material'] = $finalTotal;
            }
            $tempCollection->insert($insertArray);
        }
        /* END TRUNCATE AND ADD NEW DATA IN TEMP TABLE */
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
            0 => 'store_temp_avg_yields.id',
            1 => 'store_temp_avg_yields.batch_card_no',
            2 => 'store_temp_avg_yields.product',
            3 => 'store_temp_avg_yields.input_material',     
            4 => 'store_temp_avg_yields.sellable_qty',
            5 => 'store_temp_avg_yields.yield',
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY       
               
       
        $objTemp = new StoreTempAvgYieldModel;
        $modelQuery =  $objTemp;
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {
            if (!empty($request->custom['product-id'])) 
            {
                $custom_search = true;
                $product_id = $request->custom['product-id'];
                
                $modelQuery = $modelQuery
                            ->where('store_temp_avg_yields.product_id',$product_id);

            }
        }

        if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];

                $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('batch_card_no', 'LIKE', '%'.$search.'%');
                    $query->orwhere('product', 'LIKE', '%'.$search.'%');
                    $query->orwhere('input_material', 'LIKE', '%'.$search.'%');
                    $query->orwhere('sellable_qty', 'LIKE', '%'.$search.'%');
                    $query->orwhere('yield', 'LIKE', '%'.$search.'%');                    
                });              

            }
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_temp_avg_yields.id', 'ASC');
                        
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
            foreach ($object as $key => $row)
            {
                $data[$key]['id'] = $row->id;
                $data[$key]['batch_card_no']  = "<a class='cls-details' href=".route('admin.report.showBatch',[ base64_encode(base64_encode($row->out_id))]).">".$row->batch_card_no.'</a>';
                $data[$key]['product']  = $row->product;
                $data[$key]['input_material']  = !empty($row->input_material) ? number_format($row->input_material, 2, '.', ''): '0.00';
                $data[$key]['sellable_qty']  = !empty($row->sellable_qty) ? number_format($row->sellable_qty, 2, '.', ''): '0.00';
                $data[$key]['yield']  = !empty($row->yield) ? number_format($row->yield, 2, '.', ''): '0.00';
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
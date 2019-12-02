<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreSaleInvoiceModel;
use App\Models\StoreSaleInvoiceHasProductsModel;
use App\Models\AdminUserModel;
use App\Models\ProductsModel;
use App\Models\StoreBatchCardModel;
use App\Models\StoreSaleStockModel;

use App\Http\Requests\Admin\StoreSaleRequest;
use App\Traits\GeneralTrait;
use DB;

class StoreSalesController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(

        StoreSaleInvoiceModel $StoreSaleInvoiceModel,
        StoreSaleInvoiceHasProductsModel $StoreSaleInvoiceHasProductsModel,
        AdminUserModel $AdminUserModel,
        StoreBatchCardModel $StoreBatchCardModel,
        StoreSaleStockModel $StoreSaleStockModel
    )
    {
        $this->BaseModel  = $StoreSaleInvoiceModel;
        $this->StoreSaleInvoiceHasProductsModel  = $StoreSaleInvoiceHasProductsModel;
        $this->AdminUserModel  = $AdminUserModel;
        $this->StoreBatchCardModel  = $StoreBatchCardModel;
        $this->StoreSaleStockModel  = $StoreSaleStockModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Sales';
        $this->ModuleView  = 'admin.sales.';
        $this->ModulePath = 'admin.sales.';

        ## PERMISSION MIDDELWARE
        // $this->middleware(['permission:store-returned-material-listing'], ['only' => ['getRecords']]);
        // $this->middleware(['permission:store-returned-material-add'], ['only' => ['edit','update','create','store','destroy']]);
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
        $customers =  $this->AdminUserModel
                                ->whereHas('roles', function($query) {
                                    $query->where('name', '=','customer');
                                })
                                ->where('users.status',1)
                                ->get();
        
        $getStockProducts = $this->StoreSaleStockModel
                                ->with(['assignedProduct'])
                                ->where('company_id',$company_id)
                                ->groupBy('product_id')
                                ->get();
        //dd($getStockProducts->toArray());

        // $objProduct = new ProductsModel;
        // $products = $objProduct->getProducts($company_id);
        // $this->ViewData['products']   = $products;

        $this->ViewData['customers']  = $customers;
        $this->ViewData['getStockProducts']   = $getStockProducts;
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreSaleRequest $request)
    {        
      // dd($request->all());
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create record, Something went wrong on server.'; 
        if(empty($request->sales)) 
        {
            return response()->json($this->JsonData);
            exit();
        }

        try {  

            DB::beginTransaction();   

            $companyId = self::_getCompanyId();      
            
            ## ADD SALE
            $collection = new $this->BaseModel;   
            $collection = self::_storeOrUpdate($collection,$request);
            //dump($collection);
            if($collection)
            {
                $all_transactions = [];
                if (!empty($request->sales) && count($request->sales) > 0) 
                {                    
                    ## ADD SALE INVOICE PRODUCTS
                    foreach ($request->sales as $pkey => $sale) 
                    {
                        $saleObj = new $this->StoreSaleInvoiceHasProductsModel;
                        $saleObj->sale_invoice_id = $collection->id;
                        $saleObj->product_id = !empty($sale['product_id']) ? $sale['product_id'] : 0;
                        $saleObj->batch_id   =  !empty($sale['batch_id']) ? $sale['batch_id'] : 0;
                        $saleObj->quantity   = !empty($sale['quantity']) ? $sale['quantity'] : 0;
                        $saleObj->rate       = !empty($sale['rate']) ? $sale['rate'] : 0;
                        $saleObj->total_basic = $sale['quantity']*$sale['rate'];
                       // dd($saleObj);
                        if ($saleObj->save()) 
                        {                            
                            ## Update Sales Stock balance qty
                            $saleStock = $this->StoreSaleStockModel
                                ->where("store_sales_stock.product_id",$sale['product_id'])
                                ->where("store_sales_stock.batch_id",$sale['batch_id'])
                                ->where("store_sales_stock.company_id",$companyId)
                                ->get(['store_sales_stock.id','store_sales_stock.balance_quantity']);     

                            if(!empty($saleStock))
                            {
                                $stock = $saleStock->first();

                                $balance_quantity = $stock->balance_quantity-$sale['quantity'];
                                $updateQtyQry = DB::table('store_sales_stock')
                                                            ->where('id', $stock->id)
                                                            ->update(['balance_quantity' => $balance_quantity]);
                            }

                            $all_transactions[] = 1;
                        }
                        else
                        {
                            $all_transactions[] = 0;
                        }
                        
                    }

                }
            }

            if (!in_array(0,$all_transactions)) 
            {
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route($this->ModulePath.'index');
                $this->JsonData['msg'] = $this->ModuleTitle.' added successfully.';
                DB::commit();
            }else
            {
                DB::rollback();
                $this->JsonData['error_msg'] = $e->getMessage();
            }

        }
        catch(\Exception $e) {
            DB::rollback();
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
        // dd($encID);
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['modulePath']   = $this->ModulePath;
		
        $company_id = self::_getCompanyId();

        $data = $this->BaseModel
                    ->with(['hasSaleInvoiceProducts'=> function($q)
                            {  
                                $q->with('assignedProduct');
                                $q->with(['assignedBatch'=>function($q1){
                                    $q1->with('hasStockProducts');
                                }]);
                            },'hasCustomer'])
                    ->where('store_sale_invoice.id', base64_decode(base64_decode($encID)))
                    ->where('store_sale_invoice.company_id', $company_id)
                    ->first();
        // dd($data);
        if(empty($data)) {            
            return redirect()->route('admin.return.index');
        }

        $customers =  $this->AdminUserModel
                                ->whereHas('roles', function($query) {
                                    $query->where('name', '=','customer');
                                })
                                ->where('users.status',1)
                                ->get();

        // $objProduct = new ProductsModel;
        // $products = $objProduct->getProducts($companyId);
        // $this->ViewData['products']   = $products;

        $getStockProducts = $this->StoreSaleStockModel
                                ->with(['assignedProduct'])
                                ->where('company_id',$company_id)
                                ->groupBy('product_id')
                                ->get();

        ## ALL DATA
        $this->ViewData['object']     = $data;        
        $this->ViewData['customers']  = $customers;
        $this->ViewData['getStockProducts'] = $getStockProducts;                                
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreSaleRequest $request, $encID)
    {
        // dd($request->all());
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Sale, Something went wrong on server.';       

        if(empty($request->sales)) 
        {
            return response()->json($this->JsonData);
            exit();
        }

        $id = base64_decode(base64_decode($encID));
        try {     

            DB::beginTransaction();
            $companyId = self::_getCompanyId();      

            $collection = $this->BaseModel->find($id);   
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection)
            {
                $all_transactions = [];
                ## ADD PRODUCTION RAW MATERIAL DATA
                if (!empty($request->sales) && count($request->sales) > 0) 
                {   
                    $previousSaleProducts=$this->StoreSaleInvoiceHasProductsModel
                                                    ->where('sale_invoice_id', $collection->id)
                                                    ->get();

                    //Iterate all the previous products and update the stock balance
                    if(!empty($previousSaleProducts))
                    {
                        foreach($previousSaleProducts as $previous) 
                        {
                            $sqlQuery = "SELECT store_sales_stock.id as sssid,store_sales_stock.quantity,store_sales_stock.balance_quantity FROM store_sales_stock
                                        WHERE store_sales_stock.product_id = '".$previous->product_id."' AND store_sales_stock.batch_id = '".$previous->batch_id."'";
                            $collectionReturn = collect(DB::select(DB::raw($sqlQuery)));

                            ## Update Stock Balance quantity                            
                            if(!empty($collectionReturn) && count($collectionReturn)>0)
                            {
                                $salesData = $collectionReturn->first();
                               // dump($collectionReturn);
                                if($salesData->sssid)
                                {
                                   ##update returned quantity in production and update returned qty+actual qty in store
                                    $balance_quantity=$previous->quantity+$salesData->balance_quantity;
                                    $updateQtyQry = DB::table('store_sales_stock')
                                                                ->where('id', $salesData->sssid)
                                                                ->update(['balance_quantity' => $balance_quantity]);

                                }

                            }
                        }
                    }

                    //Delete records
                    $this->StoreSaleInvoiceHasProductsModel->where('sale_invoice_id', $collection->id)->delete();

                    //Update return quantity and lot balance for the current items
                    ## ADD IN store_has_production_materials
                    foreach ($request->sales as $pkey => $sale) 
                    {
                        if(!empty($sale['product_id']) && !empty($sale['product_id']))
                        {

                            $saleInvoiceProductObj = new $this->StoreSaleInvoiceHasProductsModel;
                            $saleInvoiceProductObj->sale_invoice_id   = $collection->id;
                            $saleInvoiceProductObj->product_id   = !empty($sale['product_id']) ? $sale['product_id'] : 0;
                            $saleInvoiceProductObj->batch_id   =  !empty($sale['batch_id']) ? $sale['batch_id'] : 0;
                            $saleInvoiceProductObj->quantity   = !empty($sale['quantity']) ? $sale['quantity'] : 0;
                            $saleInvoiceProductObj->rate   = !empty($sale['rate']) ? $sale['rate'] : 0;
                            $saleInvoiceProductObj->total_basic   =  $sale['quantity']*$sale['rate'];;
                            if ($saleInvoiceProductObj->save()) 
                            {                            
                               $all_transactions[] = 1;

                               //Remainging Minus Balance Quantity
                                ## Update Sales Stock balance qty
                                $saleStock = $this->StoreSaleStockModel
                                ->where("store_sales_stock.product_id",$sale['product_id'])
                                ->where("store_sales_stock.batch_id",$sale['batch_id'])
                                ->where("store_sales_stock.company_id",$companyId)
                                ->get(['store_sales_stock.id','store_sales_stock.balance_quantity']);     

                                if(!empty($saleStock))
                                {
                                    $stock = $saleStock->first();

                                    $balance_quantity = $stock->balance_quantity-$sale['quantity'];
                                    $updateQtyQry = DB::table('store_sales_stock')
                                                                ->where('id', $stock->id)
                                                                ->update(['balance_quantity' => $balance_quantity]);
                                }
                            }
                            else
                            {
                                $all_transactions[] = 0;
                            }

                        } 
                    }
                }//ifclose
            }

            if (!in_array(0,$all_transactions)) 
            {
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route($this->ModulePath.'index');
                $this->JsonData['msg'] = $this->ModuleTitle.' updated successfully.';
                DB::commit();
            }else
            {
                DB::rollback();
                $this->JsonData['error_msg'] = $e->getMessage();
            }

        }
        catch(\Exception $e) {
            DB::rollback();
            $this->JsonData['error_msg'] = $e->getMessage();
            $this->JsonData['msg'] = __('admin.ERR_SOMETHING_WRONG');
        }

        return response()->json($this->JsonData);
    }

    public function show($encId)
    {
        
        $id = base64_decode(base64_decode($encId));
        // dd($id);
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;

        /*$data = $this->BaseModel->with([   
                            'hasReturnedMaterials' => function($q)
                            {  
                                $q->with('material');
                                $q->with(['lot' => function($q1)
                                        {  
                                            $q1->with('hasProductionMaterial');
                                        }]
                                    );
                            },
                            'assignedProductionPlan' => function($q){
                                 $q->with(['assignedBatch'=> function($q){
                                    $q->with('assignedProduct');
                                 }]);

                            }
                        ])
                            
                    ->find($id);  */
        $data = $this->BaseModel
                    ->with(['hasSaleInvoiceProducts'=> function($q)
                            {  
                                $q->with('assignedProduct');
                                $q->with(['assignedBatch'=>function($q1){
                                    $q1->with('hasStockProducts');
                                }]);
                            },'hasCustomer'])
                    ->find($id);
                    // ->where('id',$id)
                    // ->get();
        //dump($data->toArray());
        $productBatch_data=[];
        foreach($data->hasSaleInvoiceProducts as $key => $product) {
            $productBatch_data[$product->product_id][$product->batch_id] = $product;
        }
        // dd($productBatch_data);

        $this->ViewData['object'] = $data;         
        $this->ViewData['productBatch_data'] = $productBatch_data;         
        //->find($id)->toArray(); //
        //dd($this->ViewData['object']);
        return view($this->ModuleView.'view', $this->ViewData);
    }

    public function _storeOrUpdate($collection, $request)
    {
        
        $collection->company_id   = self::_getCompanyId();
        $collection->user_id      = auth()->user()->id;
        $collection->customer_id  = $request->customer_id;
        $collection->invoice_no   = $request->invoice_no;
        $collection->invoice_date = date('Y-m-d',strtotime($request->invoice_date));   
        //Save data
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
            0 => 'store_sale_invoice.id',
            1 => 'store_sale_invoice.id',
            2 => 'store_sale_invoice.invoice_no',
            3 => 'store_sale_invoice.invoice_date',
            4 => 'users.id',
            5 => 'store_sale_invoice.id',
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/
        $companyId = self::_getCompanyId();
        ## START MODEL QUERY    
        //$modelQuery =  $this->BaseModel;
        $modelQuery =  $this->BaseModel        
                            ->join('users', 'users.id' , '=', 'store_sale_invoice.customer_id')
                            ->where('store_sale_invoice.company_id', $companyId);
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {   
            
            if (!empty($request->custom['invoice_no'])) 
            {
                $custom_search = true;

                $key = $request->custom['invoice_no'];
                $modelQuery = $modelQuery
                ->where('store_sale_invoice.invoice_no',  'LIKE', '%'.$key.'%');
            }

            if (!empty($request->custom['invoice_date'])) 
            {
                $custom_search = true;

                $dateObject = date_create_from_format("d-m-Y",$request->custom['invoice_date']);
                $invoice_date   = date_format($dateObject, 'Y-m-d'); 

                //$key = $request->custom['invoice_date'];
                $modelQuery = $modelQuery
                ->whereDate('store_sale_invoice.invoice_date', $invoice_date);
            }

            if (!empty($request->custom['customer_id'])) 
            {
                $custom_search = true;
                $key = $request->custom['customer_id'];               
                $modelQuery = $modelQuery
                ->where('users.id',  $key);               
            }
                
        }

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_sale_invoice.id', 'DESC');
            //$modelQuery = $modelQuery->orderBy('vehicles.chassis_number', 'ASC');           
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get([
                'store_sale_invoice.id', 
                'store_sale_invoice.invoice_no',
                'store_sale_invoice.invoice_date',
                'users.contact_name',
                'users.company_name',
            ]);    

         // dd($object->toArray());


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

                $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="sales[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';
                $data[$key]['invoice_no']   = $row->invoice_no;

                $data[$key]['invoice_date'] = date('d M Y',strtotime($row->invoice_date));
                $data[$key]['customer_name'] = $row->contact_name." ( ".$row->company_name." )";
                        
                $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>&nbsp';
                $view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'" title="View"><span class="glyphicon glyphicon-eye-open"></span></a>&nbsp';
                $delete = '<a href="javascript:void(0)" class="delete-user action-icon" title="Delete" onclick="return deleteCollection(this)" data-href="'.route($this->ModulePath.'destroy', [base64_encode(base64_encode($row->id))]) .'" ><span class="glyphicon glyphicon-trash"></span></a>';

                //$data[$key]['actions'] =  '<div class="text-center">'.$view.'</div>';
                //if(auth()->user()->can('store-returned-material-add'))
                //{
                    $data[$key]['actions'] =  '<div class="text-center">'.$view.$edit.$delete.'</div>';
                //}

               

         }
     }

    $customers =  $this->AdminUserModel
                        ->whereHas('roles', function($query) {
                            $query->where('name', '=','customer');
                        })
                        ->where('users.status',1)
                        ->get();

    $customer_string = '<select class="form-control select2" 
                     id="customer-id"
                     name="customer-id"><option value="">Select Customer</option> ';
    foreach ($customers as $customer) {
        $customer_string .='<option value="'.$customer->id.'" '.( $request->custom['customer_id'] == $customer->id ? 'selected' : '').'>'.$customer->contact_name .' ('.$customer->company_name.')</option>';
    }
    $customer_string .='</select>';
    
    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    $searchHTML['invoice_no']  =    '<input type="text" class="form-control" id="invoice-no" value="'.($request->custom['invoice_no']).'" placeholder="Search...">';
    $searchHTML['invoice_date']   =  '<input type="text" autocomplete="off" class="form-control datepicker" id="invoice-date" value="'.($request->custom['invoice_date']).'" placeholder="Search...">';   
    $searchHTML['customer_name'] = $customer_string;
   
    $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';

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
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to delete sale, Something went wrong on server.';

        $id = base64_decode(base64_decode($encID));

        try 
            {
                DB::beginTransaction();
                $companyId = self::_getCompanyId();

                $object = $this->BaseModel->where('id', $id)->get();
                foreach ($object as $key => $collection) 
                {
                    if(!empty($collection->id))
                    {

                        $previousSaleObject = $this->StoreSaleInvoiceHasProductsModel->where('sale_invoice_id', $collection->id)->get();
                        if(!empty($previousSaleObject) && count($previousSaleObject)>0)
                        {
                            foreach($previousSaleObject as $previous) 
                            {
                               $sqlQuery = "SELECT store_sales_stock.id as sssid,store_sales_stock.quantity,store_sales_stock.balance_quantity FROM store_sales_stock
                                            WHERE store_sales_stock.product_id = '".$previous->product_id."' AND store_sales_stock.batch_id = '".$previous->batch_id."'";
                                $collectionSale = collect(DB::select(DB::raw($sqlQuery)));

                                if(!empty($collectionSale) && count($collectionSale)>0)
                                {
                                    $salesData = $collectionSale->first();
                                    if($salesData->sssid)
                                    {
                                       ##update stock balance quantity in stock
                                        $balance_quantity=$previous->quantity+$salesData->balance_quantity;
                                        $updateQtyQry = DB::table('store_sales_stock')
                                                        ->where('id', $salesData->sssid)
                                                        ->update(['balance_quantity' => $balance_quantity]);

                                    }

                                }
                            }
                        }

                        
                        $this->StoreSaleInvoiceHasProductsModel->where('sale_invoice_id',$collection->id)->delete();

                    }
                    
                    $this->BaseModel->where('id', $collection->id)->delete();

                }

                DB::commit();

                $this->JsonData['status'] = 'success';
                $this->JsonData['msg'] = $this->ModuleTitle.' deleted successfully.';

            } catch (Exception $e) 
            {
                $this->JsonData['error_msg'] = $e->getMessage();
                $this->JsonData['msg'] = __('admin.ERR_SOMETHING_WRONG');
                DB::rollback();
            }

        return response()->json($this->JsonData);
    }

    public function bulkDelete(Request $request)
    {
        $companyId = self::_getCompanyId();

        DB::beginTransaction();
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
                $object = $this->BaseModel->whereIn('id', $arrID)->get();
                foreach ($object as $key => $collection) 
                {

                    if(!empty($collection->id))
                    {

                        $returnedMaterialObject = $this->StoreReturnedHasMaterialModel->where('returned_id', $collection->id)->get();
                    if(!empty($returnedMaterialObject) && count($returnedMaterialObject)>0)
                    {
                        foreach($returnedMaterialObject as $mkey => $mvalue) 
                        {
                           

                             $sqlQuery = "SELECT store_production_has_materials.id as spmid,store_production_has_materials.quantity,store_production_has_materials.returned_quantity FROM store_production_has_materials
                                        join store_productions ON store_production_has_materials.production_id=store_productions.id 
                                                WHERE store_productions.batch_id = '".$collection->batch_id."'
                                                AND store_productions.company_id = '".$companyId."'
                                                AND store_production_has_materials.material_id = '".$mvalue->material_id."'
                                                AND store_production_has_materials.lot_id = '".$mvalue->lot_id."'";
                            $collectionReturn = collect(DB::select(DB::raw($sqlQuery)));
                            if(!empty($collectionReturn) && count($collectionReturn)>0)
                            {
                                $returnData = $collectionReturn->first();
                                if($returnData->spmid)
                                {
                                   ##update returned quantity in production and update returned qty+actual qty in store
                                    $updateQtyQry = DB::table('store_production_has_materials')
                                                                ->where('id', $returnData->spmid)
                                                                ->update(['returned_quantity' => 0]);

                                    $updateLotBalance = $this->StoreInMaterialModel->find($mvalue->lot_id);
                                    if($updateLotBalance)
                                    {
                                        $updateLotBalance->lot_balance=($updateLotBalance->lot_balance-$returnData->returned_quantity);
                                        if($updateLotBalance->save()) 
                                        {
                                           $all_transactions[] = 1;
                                        }else{
                                           $all_transactions[] = 0;
                                        }

                                    }

                                }

                            }
                        }
                    }

                    
                    $this->StoreReturnedHasMaterialModel->where('returned_id',$collection->id)->delete();

                    }
                    
                    $this->BaseModel->where('id', $collection->id)->delete();
                }

                DB::commit();

                $this->JsonData['status'] = 'success';
                $this->JsonData['msg'] = $this->ModuleTitle.' deleted successfully.';

            } catch (Exception $e) 
            {
               $this->JsonData['exception'] = $e->getMessage();
                DB::rollback();
           }
       }

       return response()->json($this->JsonData);   
    }

    public function getPlanMaterials(Request $request)
    {
        // dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get batch materials, Something went wrong on server.';
        try 
        {
            // $material_id   = $request->material_id;
            $plan_id   = $request->plan_id;
            $companyId = self::_getCompanyId();
            // dd($companyId);
            // echo "string:".$batch_id;

            /*$raw_materials = $this->StoreRawMaterialModel
                                   ->join('store_production_has_materials','material_id','store_raw_materials.id')
                                   ->join('store_productions','store_production_has_materials.production_id','store_production_has_materials.id')
                                  ->where("store_productions.id",$plan_id)
                                  ->get([
                                        'store_raw_materials.id',
                                        'store_raw_materials.name']);*/

            $get_production_batches = $this->StoreProductionModel
                                        ->join('store_production_has_materials','production_id','store_productions.id')
                                        ->where('store_productions.id',$plan_id)
                                        ->where('company_id',$companyId)
                                        ->get(['material_id']);
                                       // ->with(['hasProductionMaterials'])
           // dd($get_production_batches->toArray());
            $material_ids = array_column($get_production_batches->toArray(), "material_id");

            $raw_materials = $this->StoreRawMaterialModel
                                  ->whereIn("id",$material_ids)
                                  ->get(['id','name']);
          
            // dd($raw_materials->toArray());
            $html="<option value=''>Select Material</option>";
            foreach($raw_materials as $material){
                $selected="";
                /*if($material_id==$material->id){
                    $selected="selected";
                } */
                
                $html.="<option value='".$material->id."' $selected>".$material->name."</option>";

            }
            // dd($get_production_batches,$raw_materials);
            /*$module = "non_material_module";
            if(!empty($material_id)){
                $html       = self::_getBatchMaterials($batch_id,$material_id,$module);
            }else{
                $html       = self::_getBatchMaterials($batch_id,false,$module);
            }*/
 
            $this->JsonData['html'] = $html;
            //$this->JsonData['data'] = $raw_materials;
            $this->JsonData['msg']  = 'Raw Materials';
            $this->JsonData['status']  = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }

    public function getProductBatches(Request $request)
    {
        // dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get product batches, Something went wrong on server.';
        try 
        {
            $company_id  = self::_getCompanyId();
            $product_id = $request->product_id;
            $selected_val = $request->selected_val;
            $editFlag = $request->editFlag;

           $getBatches = $this->StoreSaleStockModel->with(['assignedBatch'])
                                ->where("store_sales_stock.product_id",$product_id)
                                ->where("store_sales_stock.company_id",$company_id)
                                ->get();                            
            $html="<option value=''>Select Batch</option>";
            foreach($getBatches as $batch){        
                if (!in_array($batch->batch_id, $selected_val))
                {
                    $balance_quantity=$batch->balance_quantity;
                    if($editFlag==1){
                        $getqty = $this->StoreSaleInvoiceHasProductsModel
                                ->where("store_sale_invoice_has_products.batch_id",$batch->batch_id)
                                ->where("store_sale_invoice_has_products.product_id",$product_id)
                                ->first(['quantity']);   
                        if(!empty($getqty)){
                            #To add the quantity for proper validations
                            $balance_quantity=$batch->balance_quantity+$getqty->quantity;
                        }
                    }
                    $html.="<option data-qty='".$balance_quantity."' value='".$batch->batch_id."'>".$batch->assignedBatch->batch_card_no." (".$balance_quantity.")</option>";
                }                        
            }

           
            $this->JsonData['html'] = $html;
            //$this->JsonData['data'] = $raw_materials;
            $this->JsonData['msg']  = 'Product Batches';
            $this->JsonData['status']  = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }

    public function checkExistingRecord(Request $request)
    {
         // dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get material Lots, Something went wrong on server.';
        try 
        {
            $plan_id   = $request->plan_id;
            $collection = $this->BaseModel->where('plan_id',$plan_id)->first();
            // dd($collection);
            // $objStore = new StoreBatchCardModel;
            // $batcDetails = $objStore->getBatchDetails($batch_id);
            // $product = $batcDetails->assignedProduct->code." (".$batcDetails->assignedProduct->name.")";      
            //dd($collection->toArray());
            $url = '';
            if(!empty($collection)){               
                $url = route($this->ModulePath.'edit', [ base64_encode(base64_encode($collection->id))]);    
            }
            $this->JsonData['url']  = $url;
            // $this->JsonData['product']  = $product;
            $this->JsonData['msg']  = 'Raw Materials';
            $this->JsonData['status']  = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }

}
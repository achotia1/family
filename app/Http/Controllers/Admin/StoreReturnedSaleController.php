<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

## MODELS
use App\Models\StoreReturnedSaleModel;
use App\Models\StoreReturnedSalesHasProductsModel;
use App\Models\StoreSaleInvoiceModel;
use App\Models\StoreSaleInvoiceHasProductsModel;
use App\Models\AdminUserModel;
use App\Models\ProductsModel;
use App\Models\StoreBatchCardModel;
use App\Models\StoreSaleStockModel;

use App\Http\Requests\Admin\StoreReturnedSaleRequest;
use App\Traits\GeneralTrait;
use DB;

class StoreReturnedSaleController extends Controller
{

    private $BaseModel;
    use GeneralTrait;

    public function __construct(
        StoreReturnedSaleModel $StoreReturnedSaleModel,
        StoreReturnedSalesHasProductsModel $StoreReturnedSalesHasProductsModel,
        StoreSaleInvoiceModel $StoreSaleInvoiceModel,
        StoreSaleInvoiceHasProductsModel $StoreSaleInvoiceHasProductsModel,
        AdminUserModel $AdminUserModel,
        StoreBatchCardModel $StoreBatchCardModel,
        StoreSaleStockModel $StoreSaleStockModel
    )
    {
        $this->BaseModel  = $StoreReturnedSaleModel;
        $this->StoreReturnedSalesHasProductsModel = $StoreReturnedSalesHasProductsModel;
        $this->StoreSaleInvoiceModel             = $StoreSaleInvoiceModel;
        $this->StoreSaleInvoiceHasProductsModel  = $StoreSaleInvoiceHasProductsModel;
        $this->AdminUserModel       = $AdminUserModel;
        $this->StoreBatchCardModel  = $StoreBatchCardModel;
        $this->StoreSaleStockModel  = $StoreSaleStockModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Returned Sale';
        $this->ModuleView  = 'admin.return-sale.';
        $this->ModulePath = 'admin.return-sale.';

        ## PERMISSION MIDDELWARE
        // $this->middleware(['permission:store-sale-listing'], ['only' => ['getRecords']]);
        // $this->middleware(['permission:store-sale-add'], ['only' => ['edit','update','create','store','destroy']]);
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
        $invoices =  $this->StoreSaleInvoiceModel
                                ->where('company_id',$company_id)
                                ->get();

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

        $this->ViewData['invoices']  = $invoices;
        $this->ViewData['customers']  = $customers;
        $this->ViewData['getStockProducts']   = $getStockProducts;
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(StoreReturnedSaleRequest $request)
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
                    foreach ($request->sales as $pkey => $return) 
                    {
                        $saleReturnedObj = new $this->StoreReturnedSalesHasProductsModel;
                        $saleReturnedObj->returned_id = $collection->id;
                        $saleReturnedObj->product_id = !empty($return['product_id']) ? $return['product_id'] : 0;
                        $saleReturnedObj->batch_id   =  !empty($return['batch_id']) ? $return['batch_id'] : 0;
                        $saleReturnedObj->quantity   = !empty($return['quantity']) ? $return['quantity'] : 0;
                        // $saleReturnedObj->rate       = !empty($sale['rate']) ? $sale['rate'] : 0;
                        // $saleReturnedObj->total_basic = $sale['quantity']*$sale['rate'];
                       // dd($saleReturnedObj);
                        if ($saleReturnedObj->save()) 
                        {      
                            ## Update Returned qty in Sale Invoice
                            $updateQtyQry = DB::table('store_sale_invoice_has_products')
                                                            ->where('sale_invoice_id', $request->sale_invoice_id)
                                                            ->where('product_id', $return['product_id'])
                                                            ->where('batch_id', $return['batch_id'])
                                                            ->update(['returned_quantity' => $return['quantity']]);

                            ## Update Sales Stock balance qty
                            $saleStock = $this->StoreSaleStockModel
                                ->where("store_sales_stock.product_id",$return['product_id'])
                                ->where("store_sales_stock.batch_id",$return['batch_id'])
                                ->where("store_sales_stock.company_id",$companyId)
                                ->get(['store_sales_stock.id','store_sales_stock.balance_quantity']);     

                            if(!empty($saleStock))
                            {
                                $stock = $saleStock->first();

                                $balance_quantity = $stock->balance_quantity+$return['quantity'];
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
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleTitleInfo'] = $this->ModuleTitle." Information";
        $this->ViewData['modulePath']   = $this->ModulePath;
		
        $company_id = self::_getCompanyId();
        // dd($encID,$company_id);

        $data = $this->BaseModel
                    ->with(['hasReturnedProducts'=> function($q)
                            {  
                                $q->with('assignedProduct');
                                $q->with('assignedBatch');
                            },
                            'assignedCustomer',
                            'assignedSale'=> function($q)
                            {  
                                $q->with('hasSaleInvoiceProducts');
                            },
                            ])
                    ->where('store_returned_sales.id', base64_decode(base64_decode($encID)))
                    ->where('store_returned_sales.company_id', $company_id)
                    ->first();
        // dd($data);
        if(empty($data)) {            
            return redirect()->route('admin.return-sale.index');
        }

        // $customers =  $this->AdminUserModel
        //                         ->whereHas('roles', function($query) {
        //                             $query->where('name', '=','customer');
        //                         })
        //                         ->where('users.status',1)
        //                         ->get();

        $getStockProducts = $this->StoreSaleStockModel
                                ->with(['assignedProduct'])
                                ->where('company_id',$company_id)
                                ->groupBy('product_id')
                                ->get();

        ## ALL DATA
        $this->ViewData['object']     = $data;        
        // $this->ViewData['customers']  = $customers;
        $this->ViewData['getStockProducts'] = $getStockProducts;                                
        ## VIEW FILE WITH DATA
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(StoreReturnedSaleRequest $request, $encID)
    {
        // dd($request->all());
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update Returned Sale, Something went wrong on server.';       

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
                    $previousSaleReturnProducts=$this->StoreReturnedSalesHasProductsModel
                                                    ->where('returned_id', $collection->id)
                                                    ->get();

                    //Iterate all the previous products and update the stock balance
                    if(!empty($previousSaleReturnProducts))
                    {
                        foreach($previousSaleReturnProducts as $previous) 
                        {
                            ## Update Returned qty to zero
                            $updateQtyQry = DB::table('store_sale_invoice_has_products')
                                                        ->where('sale_invoice_id', $request->sale_invoice_id)
                                                        ->where('product_id', $previous->product_id)
                                                        ->where('batch_id', $previous->batch_id)
                                                        ->update(['returned_quantity' => 0]);

                            ## Update Sales Stock balance qty
                            $saleStock = $this->StoreSaleStockModel
                                ->where("store_sales_stock.product_id",$previous->product_id)
                                ->where("store_sales_stock.batch_id",$previous->batch_id)
                                ->where("store_sales_stock.company_id",$companyId)
                                ->get(['store_sales_stock.id','store_sales_stock.balance_quantity']);     

                            if(!empty($saleStock))
                            {
                                $stock = $saleStock->first();

                                $balance_quantity = $stock->balance_quantity-$previous->quantity;
                                $updateQtyQry = DB::table('store_sales_stock')
                                                            ->where('id', $stock->id)
                                                            ->update(['balance_quantity' => $balance_quantity]);
                            }
                        }
                    }

                    //Delete records
                    $this->StoreReturnedSalesHasProductsModel->where('returned_id', $collection->id)->delete();

                    //Update return quantity and stock balance for the current items
                    ## ADD IN store_has_production_materials
                    foreach ($request->sales as $pkey => $return) 
                    {
                        if(!empty($return['product_id']) && !empty($return['product_id']))
                        {

                            $saleReturnProductObj = new $this->StoreReturnedSalesHasProductsModel;
                            $saleReturnProductObj->returned_id   = $collection->id;
                            $saleReturnProductObj->product_id   = !empty($return['product_id']) ? $return['product_id'] : 0;
                            $saleReturnProductObj->batch_id   =  !empty($return['batch_id']) ? $return['batch_id'] : 0;
                            $saleReturnProductObj->quantity   = !empty($return['quantity']) ? $return['quantity'] : 0;
                           
                            if ($saleReturnProductObj->save()) 
                            {                            
                                ## Update Returned qty in Sale Invoice
                                $updateQtyQry = DB::table('store_sale_invoice_has_products')
                                        ->where('sale_invoice_id', $request->sale_invoice_id)
                                        ->where('product_id', $return['product_id'])
                                        ->where('batch_id', $return['batch_id'])
                                        ->update(['returned_quantity' => $return['quantity']]);

                                ## Update Sales Stock balance qty
                                $saleStock = $this->StoreSaleStockModel
                                ->where("store_sales_stock.product_id",$return['product_id'])
                                ->where("store_sales_stock.batch_id",$return['batch_id'])
                                ->where("store_sales_stock.company_id",$companyId)
                                ->get(['store_sales_stock.id','store_sales_stock.balance_quantity']);     

                                if(!empty($saleStock))
                                {
                                    $stock = $saleStock->first();

                                    $balance_quantity = $stock->balance_quantity+$return['quantity'];
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
       
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;

       /*$data = $this->BaseModel
                    ->with(['hasSaleInvoiceProducts'=> function($q)
                            {  
                                $q->with('assignedProduct');
                                $q->with(['assignedBatch'=>function($q1){
                                    $q1->with('hasStockProducts');
                                }]);
                            },'hasCustomer'])
                    ->find($id);*/
       
        $data = $this->BaseModel
                    ->with(['hasReturnedProducts'=> function($q)
                            {  
                                $q->with('assignedProduct');
                                $q->with('assignedBatch');
                            },
                            'assignedCustomer',
                            'assignedSale'=> function($q)
                            {  
                                $q->with('hasSaleInvoiceProducts');
                            },
                            ])
                    ->find($id);
        // dump($data->toArray());
        $productBatch_data=[];
        foreach($data->hasReturnedProducts as $key => $product) {
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
        $collection->sale_invoice_id   = $request->sale_invoice_id;
        $collection->customer_id  = $request->customer_id;
        $collection->return_date = date('Y-m-d',strtotime($request->return_date));   
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
            0 => 'store_returned_sales.id',
            1 => 'store_returned_sales.id',
            2 => 'store_sale_invoice.invoice_no',
            3 => 'store_returned_sales.return_date',
            4 => 'users.id',
            5 => 'store_returned_sales.id',
        );

            /*--------------------------------------
            |  MODEL QUERY AND FILTER
            ------------------------------*/
            $companyId = self::_getCompanyId();
            ## START MODEL QUERY    
            //$modelQuery =  $this->BaseModel;
            $modelQuery =  $this->BaseModel        
                                ->join('users', 'users.id' , '=', 'store_returned_sales.customer_id')
                                ->join('store_sale_invoice', 'store_sale_invoice.id' , '=', 'store_returned_sales.sale_invoice_id')
                                ->where('store_returned_sales.company_id', $companyId);
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

                if (!empty($request->custom['return_date'])) 
                {
                    $custom_search = true;

                    $dateObject = date_create_from_format("d-m-Y",$request->custom['return_date']);
                    $return_date   = date_format($dateObject, 'Y-m-d'); 

                    //$key = $request->custom['invoice_date'];
                    $modelQuery = $modelQuery
                    ->whereDate('store_returned_sales.return_date', $return_date);
                }

                if (!empty($request->custom['customer_id'])) 
                {
                    $custom_search = true;
                    $key = $request->custom['customer_id'];               
                    $modelQuery = $modelQuery
                    ->where('users.id',  $key);               
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
                    });
                }
            }

            ## GET TOTAL FILTER
            $filteredQuery = clone($modelQuery);            
            $totalFiltered  = $filteredQuery->count();

            ## OFFSET AND LIMIT
            if(empty($column))
            {   
                $modelQuery = $modelQuery->orderBy('store_returned_sales.id', 'DESC');
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
                    'store_returned_sales.id', 
                    'store_sale_invoice.invoice_no',
                    'store_returned_sales.return_date',
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

                    $data[$key]['return_date'] = date('d M Y',strtotime($row->return_date));
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
        $searchHTML['return_date']   =  '<input type="text" autocomplete="off" class="form-control datepicker" id="return-date" value="'.($request->custom['return_date']).'" placeholder="Search...">';   
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
        $this->JsonData['msg'] = 'Failed to delete returned sale, Something went wrong on server.';

        $id = base64_decode(base64_decode($encID));

        try 
            {
                DB::beginTransaction();
                $companyId = self::_getCompanyId();

                $object = $this->BaseModel->where('id', $id)->get();
                // dump($object);
                foreach ($object as $key => $collection) 
                {
                    if(!empty($collection->id))
                    {

                        $previousRetunedSaleObject = $this->StoreReturnedSalesHasProductsModel->where('returned_id', $collection->id)->get();
                        // dd($previousRetunedSaleObject);
                        if(!empty($previousRetunedSaleObject) && count($previousRetunedSaleObject)>0)
                        {
                            foreach($previousRetunedSaleObject as $previous) 
                            {
                                ## Update Returned qty to zero in Sale Invoice
                                $updateQtyQry = DB::table('store_sale_invoice_has_products')
                                                            ->where('sale_invoice_id', $collection->sale_invoice_id)
                                                            ->where('product_id', $previous->product_id)
                                                            ->where('batch_id', $previous->batch_id)
                                                            ->update(['returned_quantity' => 0]);

                                ## Update Sales Stock balance qty
                                $saleStock = $this->StoreSaleStockModel
                                    ->where("store_sales_stock.product_id",$previous->product_id)
                                    ->where("store_sales_stock.batch_id",$previous->batch_id)
                                    ->where("store_sales_stock.company_id",$companyId)
                                    ->get(['store_sales_stock.id','store_sales_stock.balance_quantity']);     

                                if(!empty($saleStock))
                                {
                                    $stock = $saleStock->first();

                                    $balance_quantity = $stock->balance_quantity-$previous->quantity;
                                    $updateQtyQry = DB::table('store_sales_stock')
                                                                ->where('id', $stock->id)
                                                                ->update(['balance_quantity' => $balance_quantity]);
                                }
                               
                            }
                        }

                        
                        $this->StoreReturnedSalesHasProductsModel->where('returned_id',$collection->id)->delete();

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


    public function getSaleProducts(Request $request)
    {
        // dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get sale products, Something went wrong on server.';
        try 
        {
            // $material_id   = $request->material_id;
            $sale_invoice_id   = $request->sale_invoice_id;
            $companyId = self::_getCompanyId();
            // dd($companyId);
            // echo "string:".$batch_id;

            $get_sale_products = $this->StoreSaleInvoiceHasProductsModel
                                    ->join('products','products.id','store_sale_invoice_has_products.product_id')
                                  ->where("store_sale_invoice_has_products.sale_invoice_id",$sale_invoice_id)
                                  // ->where("products.status",1)
                                  ->groupBy('store_sale_invoice_has_products.product_id')
                                  ->get([
                                        'products.id',
                                        'products.name',
                                        'products.code'
                                        ]);
            // dd($get_sale_products);    

            $html="<option value=''>Select Product</option>";
            foreach($get_sale_products as $product){
                
                $html.="<option value='".$product->id."'>".$product->code." (".$product->name.")"."</option>";

            }                              

            $invoice = $this->StoreSaleInvoiceModel
                                        ->with('hasCustomer')
                                        ->find($sale_invoice_id);

            $customerHtml="<option value=''>Select Customer</option>";
            //foreach($get_sale_customer as $invoice){
            $customerHtml.="<option value='".$invoice->hasCustomer->id."'>".$invoice->hasCustomer->contact_name." (".$invoice->hasCustomer->company_name.")"."</option>";

           // }                                          
 
            $this->JsonData['html'] = $html;
            $this->JsonData['customerHtml'] = $customerHtml;
            //$this->JsonData['data'] = $raw_materials;
            $this->JsonData['msg']     = 'Sale Products';
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
            $sale_invoice_id = $request->sale_invoice_id;
            $selected_val = $request->selected_val;
            $editFlag = $request->editFlag;

            $getBatches = $this->StoreSaleInvoiceHasProductsModel->with(['assignedBatch'])
                                ->where("store_sale_invoice_has_products.sale_invoice_id",$sale_invoice_id)
                                ->where("store_sale_invoice_has_products.product_id",$product_id)
                                ->get();  
           // dd($getBatches->toArray());                                                          
            $html="<option value=''>Select Batch</option>";
            foreach($getBatches as $batch){        
                if (!in_array($batch->batch_id, $selected_val))
                {
                    $balance_quantity=$batch->quantity;
                    /*if($editFlag==1){
                        $getqty = $this->StoreSaleInvoiceHasProductsModel
                                ->where("store_sale_invoice_has_products.batch_id",$batch->batch_id)
                                ->where("store_sale_invoice_has_products.product_id",$product_id)
                                ->first(['quantity']);   
                        if(!empty($getqty)){
                            #To add the quantity for proper validations
                            $balance_quantity=$batch->balance_quantity+$getqty->quantity;
                        }
                    }*/
                    if(!empty($balance_quantity) && $balance_quantity>0)
                    {
                        $html.="<option data-qty='".$balance_quantity."' value='".$batch->batch_id."'>".$batch->assignedBatch->batch_card_no." (".$balance_quantity.")</option>";
                    }
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
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to get material Lots, Something went wrong on server.';
        try 
        {
            $sale_invoice_id   = $request->sale_invoice_id;
            $collection = $this->BaseModel->where('sale_invoice_id',$sale_invoice_id)->first();
            $url = '';
            if(!empty($collection)){               
                $url = route($this->ModulePath.'edit', [ base64_encode(base64_encode($collection->id))]);    
            }
            $this->JsonData['url']  = $url;
            $this->JsonData['msg']  = 'Returned Sales';
            $this->JsonData['status']  = 'Success';

        } catch (Exception $e) 
        {
            $this->JsonData['exception'] = $e->getMessage();
        }

        return response()->json($this->JsonData);   
    }

    
}
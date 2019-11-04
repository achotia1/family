<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// models
use App\Models\StoreUsersModel;
//use App\User as CustomerModel;

use App\Models\ProductsModel;
use App\Models\CompanyModel;
use App\Models\UserHasProductsModel;
use App\Models\OrdersModel;

use Spatie\Permission\Models\Role;

//Request
use App\Http\Requests\Admin\CustomerRequest;

//Mail
use App\Mail\CustomerRegistrationMail; 

//Trait
use App\Traits\GeneralTrait;

use Hash;
use Mail;
use DB;

class CustomersController extends Controller
{

    private $BaseModel;
    use GeneralTrait;
  
    public function __construct(

        StoreUsersModel $StoreUsersModel,
        ProductsModel $ProductsModel,
        UserHasProductsModel $UserHasProductsModel,
        CompanyModel $CompanyModel,
        OrdersModel $OrdersModel,
        Role $RoleModel
    )
    {
        $this->BaseModel            = $StoreUsersModel;
        $this->ProductsModel        = $ProductsModel;
        $this->CompanyModel         = $CompanyModel;
        $this->UserHasProductsModel = $UserHasProductsModel;
        $this->OrdersModel          = $OrdersModel;
        $this->RoleModel            = $RoleModel;
       
        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Customer';
        $this->ModuleView  = 'admin.customers.';
        $this->ModulePath = 'admin.customers.';

        $this->middleware(['permission:manage-customers'], ['only' => ['edit','update','destroy','create','store','getRecords','assignProduucts']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Manage '.str_plural($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;     
        // dd('test');   

        // view file with data
        return view($this->ModuleView.'index', $this->ViewData);
    }

    public function assignProductsIndex($encodedCustomerId=false)
    {
        // dd($encodedCustomerId);
        $customer_id=false;
        if(!empty($encodedCustomerId)){
            $customer_id=base64_decode(base64_decode($encodedCustomerId));
        }
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Assign Products to '.str_plural($this->ModuleTitle);
        $this->ViewData['moduleAction'] = 'Assign Products to '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;     
        
        $company_id = self::_getCompanyId();
        
        $this->ViewData['customers']   = $this->BaseModel
                                               ->where('users.company_id',$company_id)
                                               ->whereStatus(1)
                                               ->whereHas('roles', function($query) {
                                                    $query->where('name',  '=','customer');
                                                })
                                               ->get();   

        $this->ViewData['products']   = $this->ProductsModel
                                            ->where('products.company_id',$company_id)
                                            ->whereStatus(1)->get();  

        $this->ViewData['customer_id']  = $customer_id;
        // view file with data
        return view($this->ModuleView.'assign-products', $this->ViewData);
    }

    public function customerProductIndex($encoded_string)
    {

        // Default site settings
        $this->ViewData['moduleAction'] = 'Assign Products to '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;     
        
        $encode_data    = json_decode(base64_decode($encoded_string));
        $user_id = $encode_data->customer_id;

        $company_id = self::_getCompanyId();
           
        $this->ViewData['customer']   = $this->BaseModel
                                               ->whereStatus(1)
                                               ->where('users.id',$user_id)
                                               ->first();   
        $this->ViewData['moduleTitle']  = 'Assign Products to '.$this->ViewData['customer']->contact_name;

        $this->ViewData['user_products'] = $this->UserHasProductsModel->with(['products'=> function($q) use($company_id)
                                                {
                                                    // dd($company_id);
                                                    $q->where('status',1);
                                                    $q->where('company_id',$company_id);
                                                }
                                            ])
                                            ->where('user_id',$user_id)
                                            ->get(); 
        
        // view file with data
        return view($this->ModuleView.'show-assigned-products', $this->ViewData);
    }

    public function customerOrderIndex($encoded_string)
    {
        // Default site settings
        $this->ViewData['moduleAction'] = str_plural($this->ModuleTitle).' Orders';
        $this->ViewData['modulePath']   = $this->ModulePath;     
        
        $encode_data    = json_decode(base64_decode($encoded_string));
        $user_id = $encode_data->customer_id;
           
        $this->ViewData['customer']   = $this->BaseModel
                                               ->where('users.id',$user_id)
                                               ->whereStatus(1)
                                               ->first();   
        
        $this->ViewData['moduleTitle']  = $this->ViewData['customer']->contact_name;
        
        /*$this->ViewData['user_products'] = $this->UserHasProductsModel->with(['products'=> function($q)
                                                {
                                                    $q->where('status',1);
                                                }
                                            ])
                                            ->where('user_id',$user_id)
                                            ->get();*/
        
        // view file with data
        return view($this->ModuleView.'orders', $this->ViewData);
    }

    public function getCustomerOrders(Request $request,$customer_id){
        // dd($request->all(),$customer_id);
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
                0 => 'orders.id',
                1 => 'orders.order_number',
                // 2 => 'users.contact_name',
                2 => 'products.name',
                3 => 'orders.delivery_date',
                4 => 'orders.dispatch_date',
                5 => 'orders.quantity',
                6 => 'orders.cost',
                7 => 'orderstatus',
                // 8 => 'orders.comment',
            );

        /*--------------------------------------
        |  Model query and filter
        ------------------------------*/

            // start model query
            $modelQuery =  $this->OrdersModel
                                ->join('users','orders.user_id','=','users.id')
                                ->join('products','orders.product_id','=','products.id')
                                ->join('order_status','orders.order_status','=','order_status.id')
                                ->where('orders.status',1)
                                ->where('orders.user_id', $customer_id);     
            // dd($modelQuery->get());
            /*if(auth()->user()->id!=1){
                if(auth()->user()->hasRole('dispatcher') || auth()->user()->hasRole('accountant')){
                    //dispatcher
                    $modelQuery = $modelQuery
                                    ->where('users.company_id', auth()->user()->company_id);      
                }else{
                    //customer
                    $modelQuery = $modelQuery
                                ->where('users.id', auth()->user()->id);
                }
            }else{
                //superadmin
                $company_id = self::_getCompanyId();

                if(!empty($company_id)){
                    $modelQuery = $modelQuery
                                    ->where('users.company_id', $company_id)
                                    ->where('users.status', 1);
                }

            }*/

            // get total count 
            $countQuery = clone($modelQuery);            
            $totalData  = $countQuery->count();

            // filter options
            $custom_search = false;
            if (!empty($request->custom))
            {

                if (!empty($request->custom['order_number'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['order_number'];

                    $modelQuery = $modelQuery
                                ->where('orders.order_number', 'LIKE', '%'.$key.'%');
                    
                }

                if (!empty($request->custom['customer'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['customer'];

                     $modelQuery = $modelQuery
                                ->where('users.contact_name', 'LIKE', '%'.$key.'%');
                    
                }
                if (!empty($request->custom['name'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['name'];

                     $modelQuery = $modelQuery
                                ->where('products.name', 'LIKE', '%'.$key.'%')
                                ->Orwhere('products.code', 'LIKE', '%'.$key.'%');
                    
                }

                /*if (!empty($request->custom['quantity'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['quantity'];

                    $modelQuery = $modelQuery
                                ->where('orders.quantity', 'LIKE', '%'.$key.'%');
                    
                }*/
                if (!empty($request->custom['delivery_date'])) 
                {
                    $custom_search = true;
                    $dateObject = date_create_from_format("m-d-Y",$request->custom['delivery_date']);
                    $delivery_date   = date_format($dateObject, 'Y-m-d'); 
                 
                    $modelQuery = $modelQuery
                                        ->whereDate('delivery_date',$delivery_date);
                }
                if (!empty($request->custom['dispatch_date'])) 
                {
                    $custom_search = true;
                    $dateObject = date_create_from_format("m-d-Y",$request->custom['dispatch_date']);
                    $dispatch_date   = date_format($dateObject, 'Y-m-d'); 
                 
                    $modelQuery = $modelQuery
                                        ->whereDate('dispatch_date',$dispatch_date);
                }

                if (!empty($request->custom['comment'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['comment'];

                    $modelQuery = $modelQuery
                                ->where('orders.comment', 'LIKE', '%'.$key.'%');
                    
                }

            }
               
            // get total filtered
            $filteredQuery = clone($modelQuery);            
            $totalFiltered  = $filteredQuery->count();
            
            // offset and limit
            if(empty($column))
            {   
                $modelQuery = $modelQuery->orderBy('orders.id', 'DESC');           
            }
            else
            {
                $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
            }

            $object = $modelQuery->skip($start)
                                    ->take($length)
                                    ->get(['orders.id', 
                                        'orders.order_number',
                                        'users.contact_name',
                                        'products.name', 
                                        'products.code', 
                                        'orders.quantity', 
                                        'orders.delivery_date', 
                                        'orders.dispatch_date', 
                                        'orders.cost', 
                                        'orders.po', 
                                        'orders.comment', 
                                        'order_status.name as orderstatus', 
                                    ]);  
       
       
        /*--------------------------------------
        |  data binding
        ------------------------------*/
            
            $data = [];

            if (!empty($object) && sizeof($object) > 0)
            {
                foreach ($object as $key => $row)
                {

                    $data[$key]['id'] = $row->id;
                    $data[$key]['order_number']     = $row->order_number;
                    $data[$key]['customer']         = $row->contact_name;
                    $data[$key]['name']             = '<span>'.ucfirst($row->name)." <br/> (".$row->code.')</span>';
                    $data[$key]['quantity']         =  $row->quantity;
                    
                    if(!empty($row->cost)){
                        $data[$key]['cost']         =  '₹'.$row->cost;
                    }else{
                        $data[$key]['cost']         =  '₹0';
                    }

                    $data[$key]['delivery_date']    =  $row->delivery_date;
                    $data[$key]['dispatch_date']    =  $row->dispatch_date;

                    /*
                    if(Storage::exists($row->po)) {
                        $po_link = url("storage/app/".$row->po);
                    } else {
                        $po_link="javascript:void(0)";
                    }    
                    $data[$key]['po']               =  '<a href="'.$po_link.'" download>VIEW</a>';//$row->po;
                    $data[$key]['comment']          =  $row->comment;*/
                    $data[$key]['orderstatus']      =  $row->orderstatus;
                    
                   
                    
                    // $data[$key]['actions'] = '';
                    

                }
            }

            /*// search html
            $searchHTML['id']       =  '';
            $searchHTML['order_number'] =  '<input type="text" class="form-control" id="order-number" value="'.($request->custom['order_number']).'" placeholder="Search...">';
            $customer_text='';
            if (!empty($request->custom['customer'])) 
            {
                $customer_text = $request->custom['customer'];
            }
            $searchHTML['customer'] =  '<input type="text" class="form-control" id="order-customer" value="'.($customer_text).'" placeholder="Search...">';
            
            $searchHTML['name']     =  '<input type="text" class="form-control" id="order-product" value="'.($request->custom['name']).'" placeholder="Search...">';
            $searchHTML['delivery_date']     = '<input type="text" class="form-control" id="order-delivery-date" value="'.($request->custom['delivery_date']).'" placeholder="Search...">';
            $searchHTML['dispatch_date']     = '<input type="text" class="form-control" id="order-dispatch-date" value="'.($request->custom['dispatch_date']).'" placeholder="Search...">';
            $searchHTML['quantity']     =  '';
            $searchHTML['cost']     =  '';//'<input type="text" class="form-control" id="order-cost" value="'.($request->custom['cost']).'" placeholder="Search...">'
            
            $searchHTML['po']     = '';
            $searchHTML['comment']     =  '';//'<input type="text" class="form-control" id="order-comment" value="'.($request->custom['comment']).'" placeholder="Search...">';
            $searchHTML['orderstatus']     = '';
                
            if ($custom_search) 
            {
                $seachAction  =  '<a style="cursor:pointer;" onclick="return removeSearch(this)" class="blue-btn-inverse">Remove Filter</a>';
            }
            else
            {
                $seachAction  =  '<a style="cursor:pointer;" onclick="return doSearch(this)" class="blue-btn">Search</a>';
            }

            $searchHTML['actions'] = $seachAction;
            array_unshift($data, $searchHTML);*/

        // wrapping up
        $this->JsonData['draw']             = intval($request->draw);
        $this->JsonData['recordsTotal']     = intval($totalData);
        $this->JsonData['recordsFiltered']  = intval($totalFiltered);
        $this->JsonData['data']             = $data;

        return response()->json($this->JsonData);
    }

    public function assignProduucts(Request $request){

       // dd($request->all());
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to assign products to customer, Something went wrong on server.'; 

        DB::beginTransaction();

        $user_id = $request->user_id;
        $product_ids = explode(",", $request->product_ids);
        try {

            if(count($product_ids)>0){
                
                $this->UserHasProductsModel->where('user_id', $user_id)->delete();
                // DB::rollback();
                // dd('ss');
                $userproducts = [];
                foreach ($product_ids as $product_id) {
                    $userproducts[] = array('user_id'=> $user_id, 'product_id'=> $product_id);
                }
                // dd($userproducts);

                if($this->UserHasProductsModel->insert($userproducts))
                {
                    DB::commit();
                    $this->JsonData['status']   = __('admin.RESP_SUCCESS');
                    $this->JsonData['url']      = route($this->ModulePath.'index');
                    $this->JsonData['msg']      = 'Assigned products to customer successfully.';
                }
                
            }

        }
        catch(\Exception $e) {
            DB::rollback();
            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);

    }

    public function getCustomerProducts(Request $request){
        // dd($request->all());
        $user_id = $request->user_id;
         $company_id = self::_getCompanyId();
        
        $products   = $this->ProductsModel
                            ->where('products.company_id', $company_id)
                            ->whereStatus(1)->get();
        //dd($products->toArray());
        /*$data    = $this->UserHasProductsModel->with(['products'=> function($q)
                                            {
                                                $q->where('status',1);
                                            }
                                        ])
                                        ->where('user_id',$user_id)
                                        ->get();*/
        $user_products    = $this->UserHasProductsModel
                                        ->where('user_id',$user_id)
                                        ->get(); 
        $product_ids=array_column($user_products->toArray(), 'product_id');

        //dd($product_ids);
        $productsHtml='';
        if(count($products)>0){
            foreach ($products as $product) {
            // dd($userproducts->products->id,$userproducts->products->name,$userproducts->products->code);
                $selected="";
                if(in_array($product->id, $product_ids)){
                    $selected="selected";
                }
                $productsHtml.= '<option value="'.$product->id.'" '.$selected.' data-subtext="('.$product->code.')">'. $product->name.'</option>';
            }                               
        }
        
        $this->JsonData['productsHtml']     = $productsHtml;
        $this->JsonData['msg']      = 'Customer Products';
        return response()->json($this->JsonData);
    }

    public function create()
    {
       // Default site settings
        $this->ViewData['moduleTitle']  = 'Add New '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Add New '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        //$this->ViewData['companies']   = $this->CompanyModel->whereStatus(1)->get();

        // view file with data
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(CustomerRequest $request)
    {   
        //Check Email is already registered in current company or registered in another companies
        if(!empty($request->email)){
            $id = false;
            $email      = $request->email;
            $countEmail = self::_checkCompanyUniqueUser($id,$email);
            if(!empty($countEmail) && $countEmail->cnt>0){
                $error = 'Email has already taken.';
                if($countEmail->currentCompany!=$countEmail->company_id){
                    $error = 'Email is registered in another company.';
                }

                $this->JsonData['status']    = __('admin.RESP_ERROR');
                //$this->JsonData['msg']       = 'The given data was invalid.';      
                $this->JsonData['errors']['email'][0] = $error;      

                return response()->json($this->JsonData);  
            }
        }

        // DB::beginTransaction();
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create Customer, Something went wrong on server.'; 

        try {

            $collection     = new $this->BaseModel;   
            $request->add   = 1;
            $collection     = self::_storeOrUpdate($collection,$request);
            if ($collection) 
            {
                $role = 'customer';
                $collection->assignRole($role);
                
                // flush permission cache
                app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] =  route($this->ModulePath.'index');
                $this->JsonData['msg'] = 'Customer created successfully.'; 
            }
            

        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
    }

    public function edit($encID)
    {
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        // All data
        $this->ViewData['customer'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));
        //$this->ViewData['companies'] = $this->CompanyModel->whereStatus(1)->get();
        
        // view file with data
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(CustomerRequest $request, $encID)
    {

        $id = base64_decode(base64_decode($encID));
        //Check Email is already registered in current company or registered in another companies
        if(!empty($request->email)){
        
            $email      = $request->email;
            $countEmail = self::_checkCompanyUniqueUser($id,$email);
            if(!empty($countEmail) && $countEmail->cnt>0){
                $error = 'Email has already taken.';
                if($countEmail->currentCompany!=$countEmail->company_id){
                    $error = 'Email is registered in another company.';
                }

                $this->JsonData['status']    = __('admin.RESP_ERROR');
                // $this->JsonData['msg']       = 'The given data was invalid.';      
                $this->JsonData['errors']['email'][0] = $error;      

                return response()->json($this->JsonData);  
            }
        }

        // dd($request->all());
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update customer, Something went wrong on server.';       
              
        try {

            $collection = $this->BaseModel->find($id);   
            $request->add = 0;
            $collection = self::_storeOrUpdate($collection,$request);
            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route($this->ModulePath.'index');
                $this->JsonData['msg'] = 'Customer Updated successfully.'; 
            }

            
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
    }

    public function destroy($encID)
    {
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to delete user, Something went wrong on server.';

        $id = base64_decode(base64_decode($encID));

        if ((int)$id === (int)auth()->user()->id) 
        {
            $this->JsonData['status'] = 'error';
            $this->JsonData['msg'] = 'Can\'t delete current logged user';
            return response()->json($this->JsonData);
            exit;
        }

        $BaseModel = $this->BaseModel->find($id);
        // $BaseModel->status = 0;
        if($BaseModel->delete())
        {
            $BaseModel->syncRoles([]);
            $this->JsonData['status'] = 'success';
            $this->JsonData['msg'] = 'Customer deleted successfully.';
        }

        return response()->json($this->JsonData);
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
            /*$filter = array(
                0 => 'users.id',
                1 => 'users.id',
                2 => 'users.contact_name',
                3 => 'users.email',
                4 => 'users.company_name',
            );*/
            $filter = array(
                0 => 'users.id',
                1 => 'users.contact_name',
                2 => 'users.company_name',
                3 => 'users.email',
                4 => 'users.mobile_number',
                5 => 'users.id',
            );

        /*--------------------------------------
        |  Model query and filter
        ------------------------------*/

            // start model query
            $modelQuery =  $this->BaseModel
                                ->leftjoin('companies','users.company_id','=','companies.id')
                                ->whereHas('roles', function($query) {
                                    $query->where('name', '=','customer');
                                })
                                ->where('users.status',1);
            
            $company_id = self::_getCompanyId();
            //if(auth()->user()->company_id>0){
                  $modelQuery = $modelQuery
                                ->where('users.company_id',$company_id);
            //}
            

            // get total count 
            $countQuery = clone($modelQuery);            
            $totalData  = $countQuery->count();

            // filter options
            $custom_search = false;
            if (!empty($request->custom))
            {

                if (!empty($request->custom['contact_name'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['contact_name'];

                     $modelQuery = $modelQuery
                                ->where('users.contact_name', 'LIKE', '%'.$key.'%');
                    
                }

                if (!empty($request->custom['email'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['email'];

                    $modelQuery = $modelQuery
                                ->where('users.email', 'LIKE', '%'.$key.'%');
                    
                }

                if (!empty($request->custom['company_name'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['company_name'];

                    $modelQuery = $modelQuery
                                ->where('users.company_name', 'LIKE', '%'.$key.'%');

                    // $modelQuery = $modelQuery
                    //             ->where('companies.id', 'LIKE', '%'.$key.'%');
                    
                }

            }
               
            // get total filtered
            $filteredQuery = clone($modelQuery);            
            $totalFiltered  = $filteredQuery->count();
            
            // offset and limit
            if(empty($column))
            {   
                $modelQuery = $modelQuery->orderBy('users.id', 'DESC');           
            }
            else
            {
                $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
            }

            $object = $modelQuery->skip($start)
                                    ->take($length)
                                    ->get(['users.id', 
                                        'users.contact_name', 
                                        'users.email', 
                                        'users.mobile_number', 
                                        'users.company_name', 
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

                     
                    $data[$key]['contact_name']  = '<span title="'.ucfirst($row->contact_name).'">'.str_limit(ucfirst($row->contact_name), '60', '...').'</span>';
                    $data[$key]['email']        = '<a title="'.$row->email.'" href="mailto:'.$row->email.'" target="_blank" >'.strtolower($row->email).'</a>';
                    
                    $data[$key]['company_name']  =  $row->company_name;
                    $data[$key]['mobile_number']  =  "<a href='tel:".$row->mobile_number."'>".$row->mobile_number."</a>";
                    
                    $encode_data['customer_id']  =   $row->id;
                    $encode_string=base64_encode(json_encode($encode_data));
                    $customer_product_url = route($this->ModulePath.'customerproductindex', [$encode_string]);

                    $customer_orders_url = route('admin.customers.customerorderindex', [$encode_string]);
                    //$customer_orders_url = '/admin/orders/'.$encode_string;
                    
                    $orders_cnt = self::_getCustomerOrdersCount($row->id);
                    
                    if($orders_cnt){
                        $data[$key]['orders']  = '<a href="'.$customer_orders_url.'">'. $orders_cnt->cnt.'</a>';
                    }else{
                        $data[$key]['orders']  =  0;
                    }

                    $customer_assigned_product = '<a href="'.$customer_product_url.'"><img src="'.url('/assets/admin/images').'/icons/sidebar/1/Users-Active.png" alt=" Assigned Product" title="Assigned Product"></a>';
                    
                    
                   // $view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'"><img src="'.url('/assets/admin/images').'/icons/eye.svg" alt=" view"></a>';
                    $edit = '<a href="'.route($this->ModulePath.'edit', [ base64_encode(base64_encode($row->id))]).'"><img src="'.url('/assets/admin/images').'/icons/edit.svg" alt=" edit" title="edit"></a>';

                    $delete = '<a href="javascript:void(0)" onclick="return deleteCollection(this)" data-href="'.route('admin.customers.destroy', [base64_encode(base64_encode($row->id))]) .'" ><img src="'.url('/assets/admin/images').'/icons/delete.svg" alt=" delete" title="delete"></a>';

                    if ((int)$row->id === (int)auth()->user()->id) 
                    {
                        $delete = '';
                    }

                    
                    //$data[$key]['actions'] = '<div class="text-center">'.$view.'</div>';
                    
                    //if(auth()->user()->can('manage-bundles'))
                    //{
                        $data[$key]['actions'] =  '<div class="text-center">'.$customer_assigned_product.$edit.$delete.'</div>';
                    //}

                }
            }

            // search html
            $searchHTML['id']       =  '';
            //$searchHTML['select']   =  '';
            $searchHTML['mobile_number']       =  '';
            $searchHTML['orders']       =  '';
            $searchHTML['contact_name']     =  '<input type="text" class="form-control" id="contact-name" value="'.($request->custom['contact_name']).'" placeholder="Search...">';
            $searchHTML['email']     =  '<input type="text" class="form-control" id="email" value="'.($request->custom['email']).'" placeholder="Search...">';
            $searchHTML['company_name']     =  '<input type="text" class="form-control" id="company-name" value="'.($request->custom['company_name']).'" placeholder="Search...">';
            //<input type="text" class="form-control" id="company-name" value="'.($request->custom['company_name']).'" placeholder="Search...">
                
            if ($custom_search) 
            {
                $seachAction  =  '<a style="cursor:pointer;" onclick="return removeSearch(this)" class="blue-btn-inverse">Remove Filter</a>';
            }
            else
            {
                $seachAction  =  '<a style="cursor:pointer;" onclick="return doSearch(this)" class="blue-btn">Search</a>';
            }

            $searchHTML['actions'] = $seachAction;
            

            array_unshift($data, $searchHTML);

        // wrapping up
        $this->JsonData['draw']             = intval($request->draw);
        $this->JsonData['recordsTotal']     = intval($totalData);
        $this->JsonData['recordsFiltered']  = intval($totalFiltered);
        $this->JsonData['data']             = $data;

        return response()->json($this->JsonData);
    }

    public function _getCustomerOrdersCount($user_id){

        // preparing where conditions
        $collections = collect([]);
        if (!empty($user_id)) {
                //$date ='2019-05-13';
                // query to get total records
                $sqlQuery = "SELECT COUNT(orders.id) AS cnt FROM orders join  users on orders.user_id = users.id WHERE orders.user_id= '".$user_id."' and orders.status=1";
            $collections = collect(DB::select(DB::raw($sqlQuery)));
        }
   

        return $collections->first();

    }

    public function _storeOrUpdate($collection, $request)
    {
        // dd(url('/'));
        // dump(app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions());
        //     dd('pass');
        $collection->contact_name   = $request->contact_name;
        $collection->mobile_number  = $request->mobile_number;
        //$collection->company_id     = base64_decode(base64_decode($request->company_id));
        $collection->company_name   = $request->company_name;
        $collection->company_id     = self::_getCompanyId();
        $collection->email          = $request->email;
        $collection->status         = 1;//Active
        
        if($request->add==1){
            $collection->username       = $request->contact_name;
            $password                   = self::_generatePassword(6);
            $collection->str_password   = $password;
            $collection->password       = Hash::make($password);
            
            $phone   = str_replace("-", "",$collection->mobile_number);
            $site_url = url('/');
            $company_name = "";

            $company_id = self::_getCompanyId();
            $company = "";
            if(!empty($company_id)){
                $company = $this->CompanyModel->find($company_id);
            }

            if(!empty($company->name)){
                $company_name = $company->name;
            }
            $message = 'Hi '.$collection->contact_name.',Username: '.$collection->email.' Password: '.$collection->str_password;//.' ,Connect us at: '.$site_url
            $message .= " Thanks,".$company_name;
            self::_sendSms($phone,$message);

            //Send Mail
            self::_sendRegisterMail($collection,$company);

        }
        
        //Save data
        $collection->save();
        // $collection->assignRole('customer');
        // $collection->syncRoles(['customers']);
        
        return $collection;
    }

    public function _generatePassword($length = 20){
      $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.'0123456789';
                //`-=~!@#$%^&*()_+,./<>?;:[]{}\|

      $str = '';
      $max = strlen($chars) - 1;
        for ($i=0; $i < $length; $i++)
            $str .= $chars[mt_rand(0, $max)];

      return $str;
    }

    public function _sendRegisterMail($collection,$company){
        $mail_collection = new $collection;
        $mail_collection->contact_name = $collection->contact_name;
        $mail_collection->email = $collection->email;
        $mail_collection->str_password = $collection->str_password;
        
        if(!empty($company->logo) && is_file(storage_path().'/app/'.$company->logo)){
            $logo = 'storage/app/'.$company->logo;
        }else{
            $logo = 'assets/admin/images/logo.jpg';
        }
        $mail_collection->logo = url($logo);

        $mail_collection->company_name = "";
        if(!empty($company->name)){
            $mail_collection->company_name = $company->name;
        }
        $mail_collection->adminmail = config('constants.ADMINEMAIL');
        $mail_collection->login_url = url('/admin/login');
        $mail_collection->company_url = url('/');

        $result = Mail::to($mail_collection->email)->send(new CustomerRegistrationMail($mail_collection));
    }

    public function showCustomerProfile($encID)
    {
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Edit Profile';
        $this->ViewData['moduleAction'] = 'Edit Profile';
        $this->ViewData['modulePath']   = $this->ModulePath;

        // All data
        $this->ViewData['customer'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));
        //$this->ViewData['companies'] = $this->CompanyModel->whereStatus(1)->get();


        // view file with data
        return view($this->ModuleView.'edit-profile', $this->ViewData);
    }

    public function updateCustomerProfile(CustomerRequest $request, $encID)
    {
        // dd($request->all(),$encID);

        $id = base64_decode(base64_decode($encID));
        //Check Email is already registered in current company or registered in another companies
        if(!empty($request->email)){
        
            $email      = $request->email;
            $countEmail = self::_checkCompanyUniqueUser($id,$email);
            if(!empty($countEmail) && $countEmail->cnt>0){
                $error = 'Email has already taken.';
                if($countEmail->currentCompany!=$countEmail->company_id){
                    $error = 'Email is registered in another company.';
                }

                $this->JsonData['status']    = __('admin.RESP_ERROR');
                // $this->JsonData['msg']       = 'The given data was invalid.';      
                $this->JsonData['errors']['email'][0] = $error;      

                return response()->json($this->JsonData);  
            }
        }

        // dd($request->all());
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update profile, Something went wrong on server.';       
              
        try {

            $collection = $this->BaseModel->find($id);   
            $request->add = 0;
            $collection = self::_storeOrUpdate($collection,$request);
            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route('admin.dashboard');
                $this->JsonData['msg'] = 'Your Profile Updated successfully.'; 
            }
            
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
    }
}

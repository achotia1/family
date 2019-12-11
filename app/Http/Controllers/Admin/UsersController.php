<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\StoreUsersModel;
use Spatie\Permission\Models\Role;
use App\Models\CompanyModel;

// Request
use App\Http\Requests\Admin\UsersRequest;
use App\Http\Requests\Admin\UserUpdatePasswordRequest;

//Trait
use App\Traits\GeneralTrait;

//Mail
use App\Mail\CustomerRegistrationMail; 

// plugins
use Hash;
use Mail;
use DB;
use Auth;

class UsersController extends Controller
{
    private $BaseModel;
    use GeneralTrait;

    public function __construct(
        StoreUsersModel $StoreUsersModel,
        Role $RoleModel,
        CompanyModel $CompanyModel
    )
    {
        $this->BaseModel  = $StoreUsersModel;
        $this->StoreUsersModel  = $StoreUsersModel;
        $this->CompanyModel         = $CompanyModel;
        $this->RoleModel  = $RoleModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Users';
        $this->ModuleView  = 'admin.users.';
        $this->ModulePath = 'admin.users';
    }

    public function index()
    {
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Manage '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Manage '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        // $this->ViewData['users'] = $this->StoreUsersModel->orderBy('id', 'DESC')->get();

        // view file with data
        return view($this->ModuleView.'index', $this->ViewData);
    }

    public function create()
    {
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Manage '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Create '.str_singular($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;

        // All userdata
        $this->ViewData['users'] = $this->BaseModel->get();

        // view file with data
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function store(UsersRequest $request)
    {
        // dd('store');
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
                // $this->JsonData['msg']       = 'The given data was invalid.';      
                $this->JsonData['errors']['email'][0] = $error;      

                return response()->json($this->JsonData);  
            }
        }
         // dd('pp');

        DB::beginTransaction();
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = __('admin.FAIL_USER_CREATE');

        try {

            $collection     = new $this->BaseModel;   
            $request->add   = 1;
            $collection     = self::_storeOrUpdate($collection,$request);
            if ($collection) 
            {
                // attach role
                $role = $this->RoleModel->where('id', base64_decode(base64_decode($request->role)))
                                        ->pluck('name')
                                        ->first();
                try {

                    $collection->assignRole(strtolower($role));

                     DB::commit();

                    $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                     $this->JsonData['url'] =  route($this->ModulePath.'.index');
                    $this->JsonData['msg'] = __('admin.USER_CREATED');
                    

                }
                catch(\Exception $e) {

                    $this->JsonData['exception'] = $e->getMessage();
                    DB::rollback();
                }
            }
            else
            {
                 DB::rollback();
            }

             // flush permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
    }

    public function show($id)
    {
        dd('show');
    }

    public function edit($encID)
    {
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Manage '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.str_singular($this->ModuleTitle);
        $this->ViewData['modulePath']   = $this->ModulePath;

        // All userdata
        $id = base64_decode(base64_decode($encID));
        $this->ViewData['customer'] = $this->BaseModel->find($id);
    
        // view file with data
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(UsersRequest $request, $encID)
    {
        DB::beginTransaction();
        
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
        $this->JsonData['msg'] = __('admin.FAIL_USER_CREATE');       
              
        try {

            $collection = $this->BaseModel->find($id);   
            $request->add = 0;
            $collection = self::_storeOrUpdate($collection,$request);
            if ($collection) 
            {
                // attach role
                if(!empty($request->role))
                {
                    $roleCollection = $this->RoleModel->where('id', base64_decode(base64_decode($request->role)))->first();
                    try {

                        $collection->syncRoles(strtolower($roleCollection->name));
                        $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                        $this->JsonData['url'] = route($this->ModulePath.'.index');
                        $this->JsonData['msg'] = __('admin.USER_UPDATED');
                        DB::commit();
                    }
                    catch(\Exception $e) {

                        $this->JsonData['exception'] = $e->getMessage();
                        DB::rollback();
                    }
                }
                else
                {
                    $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                    $this->JsonData['url'] = route($this->ModulePath.'.index');
                    $this->JsonData['msg'] = __('admin.USER_UPDATED');
                    DB::commit();
                }
            }
            else
            {
                 DB::rollback();
            }
            
            // flush permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        

            
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
    }

    public function updatePassword(UserUpdatePasswordRequest $request)
    {
        // dd($request->all(),auth()->user()->id,auth()->user()->email);
        $new_pasword = $request->password;
        if (!empty($new_pasword)) 
        {
            $collection = $this->BaseModel
                        ->where('id', auth()->user()->id)
                        ->where('email', auth()->user()->email)
                        ->first();

            if (!empty($collection)) 
            {
                if (Hash::check($request->old_password, $collection->password))        
                {
                    $collection->password       = Hash::make($new_pasword);
                    $collection->str_password   = $new_pasword;
                    if($collection->save())
                    {
                        $this->JsonData['status'] = 'success';
                        $this->JsonData['msg'] = 'Password updated successfully.';
                    }
                    else
                    {
                        $this->JsonData['status'] = 'error';
                        $this->JsonData['msg'] = 'Failed to update password, Something went wrong on server.';
                    }
                }
                else
                {
                    $this->JsonData['status'] = 'error';
                    $this->JsonData['msg'] = 'Old password does not match with the password you logged in.';
                }
            }
            else
            {
                $this->JsonData['status'] = 'error';
                $this->JsonData['msg'] = 'Session timeout, Please try again after login.';
            }
        }
        else
        {
            $this->JsonData['status'] = 'error';
            $this->JsonData['msg'] = 'New password field is required.';
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
        $BaseModel->syncRoles([]);  
        if($BaseModel->delete())
        {
            $this->JsonData['status'] = 'success';
            $this->JsonData['msg'] = 'User deleted successfully.';
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
            $filter = array(
                0 => 'id',
                1 => 'store_users.name',
                2 => 'store_users.email',
                3 => 'store_users.mobile_number',
                4 => 'role',
                5 => 'role',
                // 4 => 'status'
            );

        /*--------------------------------------
        |  Model query and filter
        ------------------------------*/

            // start model query
            $modelQuery =  $this->BaseModel
                            ->whereHas('roles', function($query) {
                                $query->where('guard_name', 'admin');
                               // $query->where('name', '!=','customer');
                            });

             $company_id = self::_getCompanyId();
            //if(auth()->user()->company_id>0){
                  $modelQuery = $modelQuery
                                ->whereIn('store_users.company_id',[$company_id,0]);
                                //->orwhere('users.company_id', 0);
            //}
            // dd($modelQuery);                    
            // get total count 
            $countQuery = clone($modelQuery);            
            $totalData  = $countQuery->count();

            // filter options
            if (!empty($request->search))
            {
                if (!empty($request->search['value'])) 
                {
                    $search = $request->search['value'];

                     $modelQuery = $modelQuery->where(function ($query) use($search)
                    {
                        $query->orwhere('store_users.name', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('store_users.email', 'LIKE', '%'.$search.'%');   
                        $query->orwhere('store_users.mobile_number', 'LIKE', '%'.$search.'%');   
                    });
                }
            }

            // get total filtered
            $filteredQuery = clone($modelQuery);            
            $totalFiltered  = $filteredQuery->count();
            
            // offset and limit
            $object = $modelQuery->orderBy($filter[$column], $dir)
                                 ->skip($start)
                                 ->take($length)
                                 ->get();            
            // dd($object);
        /*--------------------------------------
        |  data binding
        ------------------------------*/
            $data = [];
            if (!empty($object) && sizeof($object) > 0) 
            {
                foreach ($object as $key => $row) 
                {
                    if (!$row->hasRole('super-admin') || auth()->user()->hasRole('super-admin')) 
                    {

                        $data[$key]['id']           = $row->id;
                        $data[$key]['name']   = '<span title="'.ucfirst($row->name).'">'.ucfirst($row->name).'</span>';
                        // $data[$key]['company_name']    = '<span title="'.ucfirst($row->company_name).'">'.ucfirst($row->company_name).'</span>';
                        $data[$key]['email']        = '<a title="'.$row->email.'" href="mailto:'.$row->email.'" target="_blank" >'.strtolower($row->email).'</a>';                        
                        $data[$key]['mobile_number']  =  "<a href='tel:".$row->mobile_number."'>".$row->mobile_number."</a>";
                        $data[$key]['role']         = ucfirst($row->getRoleNames()[0] ?? '');
                        
                        
                        /*if (!empty($row->status)) 
                        {
                            $data[$key]['status'] = '<span class="theme-green semibold text-center f-18" >Active</i></span>';
                        }
                        else
                        {
                            $data[$key]['status'] = '<span class="theme-black-light semibold text-center f-18" >Inactive</i></span>';
                        }*/

                        $edit = '<a href="'.route('admin.users.edit', [ base64_encode(base64_encode($row->id))]).'" class="edit-user action-icon" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>&nbsp&nbsp';
                        $delete = '<a href="javascript:void(0)" class="delete-user action-icon" title="Delete" onclick="return deleteCollection(this)" data-href="'.route('admin.users.destroy', [base64_encode(base64_encode($row->id))]) .'" ><span class="glyphicon glyphicon-trash"></span></a>';

                        if ((int)$row->id === (int)auth()->user()->id) 
                        {
                            $delete = '';
                        }

                        $data[$key]['actions'] = '<div class="text-center">'.$edit.$delete.'</div>';
                    }
                }
            }

            // wrapping up
            $this->JsonData['draw']             = intval($request->draw);
            $this->JsonData['recordsTotal']     = intval($totalData);
            $this->JsonData['recordsFiltered']  = intval($totalFiltered);
            $this->JsonData['data']             = $data;

        return response()->json($this->JsonData);
    }    
 
    public function _storeOrUpdate($collection, $request)
    {
        // dump(app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions());
        // dump($request->all());
        // dd($request->add);
        $collection->name   = $request->name;
        $collection->mobile_number  = str_replace("-", "", $request->mobile_number);
        //$collection->company_id     = base64_decode(base64_decode($request->company_id));
        // $collection->company_name   = $request->company_name;
        $collection->email          = $request->email;
        $collection->status         = 1;//Active
        // dd($collection);
        $collection->username       = $request->username;
        if($request->add==1){
            // $collection->username       = strtolower(str_replace(" ", "", $request->name));
           // $password                   = self::_generatePassword(6);
            if(!empty($request->password))
            {
                $collection->str_password   = $request->password;
                $collection->password   = Hash::make($request->password);
            }

            // $collection->password       = Hash::make($password);
            
            $collection->company_id     = self::_getCompanyId();
            //new
            /*$phone   = str_replace("-", "",$collection->mobile_number);
            $site_url = url('/');
            $company_name = "";

            $company_id = $collection->company_id;
            $company = "";
            if(!empty($company_id)){
                $company = $this->CompanyModel->find($company_id);
            }

            if(!empty($company->name)){
                $company_name = $company->name;
            }*/
            /*$message = 'Hi '.$collection->name.',Username: '.$collection->email.' Password: '.$collection->str_password;//.' ,Connect us at: '.$site_url
            $message .= " Thanks,".$company_name;
            self::_sendSms($phone,$message);*/

            //Send Mail
           //self::_sendRegisterMail($collection);
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
}

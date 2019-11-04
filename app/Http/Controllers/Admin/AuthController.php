<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\StoreUsersModel;
use App\Models\CompanyModel;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\ForgotPasswordRequest;
use App\Mail\ForgotPasswordMail; 
use App\PasswordReset;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;
/*
use App\Models\RememberMeModel;
*/
use \Illuminate\Auth\Passwords\PasswordBroker;

use Spatie\Permission\Models\Permission;

use Hash;
use Mail;
use Cookie;
use Carbon\Carbon;

// temp
// use Spatie\Permission\Models\Role;

use App\Traits\GeneralTrait;
use Session;

class AuthController extends Controller
{   
    private $BaseModel;
    private $ViewData;
    private $JsonData;
    private $ModuleTitle;
    private $ModuleView;
    private $ModulePath;

    use GeneralTrait;

    public function __construct(
       
        StoreUsersModel $StoreUsersModel,
        // RememberMeModel $RememberMeModel,
        PasswordReset $PasswordResetModel,
        PasswordBroker $PasswordBroker,
        CompanyModel $CompanyModel
    )
    {

        $this->BaseModel = $StoreUsersModel;   
        // $this->RememberMeModel = $RememberMeModel;   
        $this->PasswordResetModel = $PasswordResetModel;
        $this->PasswordBroker = $PasswordBroker;
        $this->CompanyModel = $CompanyModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleView  = 'admin.auth.';
        $this->ModulePath = 'admin.auth.';   
        
        $this->rememberTitle = 'LARAVEL_RSESSION';
        
        // Session::put('company_id', 1);
        // dd(Session::get('company_id'));
        // $companyId = self::_getCompanyId();
        // dd($companyId);
    }

    /*---------------------------------
    |   LOGIN AND LOGOUT
    ------------------------------------------*/

        public function login(Request $request)
        {
            $this->ViewData['moduleTitle']  = 'User Login';
            $this->ViewData['moduleAction'] = 'USER LOG IN';
            $this->ViewData['modulePath']   = $this->ModulePath.'login';
            
            /*if (!empty($_COOKIE[$this->rememberTitle])) 
            {
                $token = $_COOKIE[$this->rememberTitle];

                $this->ViewData['user'] = $this->RememberMeModel
                                        ->where('remember_token', $token)
                                        ->first();
            }*/
            //dd('login');

            return view($this->ModuleView.'login', $this->ViewData);
        }

        public function checkLogin(LoginRequest $request,$enCompanyId=false)
        {
             //dd($request);
             // dump(app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions());
            // dd('p',$enCompanyId);
            $this->JsonData['status'] = 'error';
            $this->JsonData['msg'] = 'Incorrect login details.';

            // check for valid username 
            $credentials = [];
            $credentials['email']    = self::_validateUsername($request->username);
            $credentials['password'] = $request->password;
            $remember_me = !empty($request->remember) ? true : false;
            
            /*$user = $this->BaseModel->where('email',$credentials['email'])->first();
            dd($user->getRoleNames());*/

            // dd(auth()->user()->roles->pluck('guard_name')->first());
            if (auth()->guard('admin')->attempt($credentials, $remember_me)) 
            {
               
                if(empty($enCompanyId) && auth()->user()->id==1){
                    $this->JsonData['status'] = 'error';
                    $this->JsonData['msg'] = 'Dear Super admin,You are trying to login without selecting company';
                    auth()->logout();
                    return response()->json($this->JsonData);exit;     
                }
                
                if(empty($enCompanyId) && !auth()->user()->hasAnyPermission(['store-dashboard'])){

                    $this->JsonData['status'] = 'error';
                    $this->JsonData['msg'] = 'Dear User,You do not have any permissions to access the system, Please contact website administrator.';
                    auth()->logout();
                    return response()->json($this->JsonData);exit;     
                }
 // dd('test2');
// dd(auth()->user()->roles->pluck('guard_name')->first());
                if (auth()->user()->roles->pluck('guard_name')->first() === 'admin') 
                {   
                    //dd(auth()->guard('admin')->user());
                    // dd(auth()->user());
                    if (auth()->user()->status) 
                    {
                        $companyId = self::_setCompanyId(base64_decode($enCompanyId));
                        //self::_applyOrDestroyRemember($remember_me, $request);
                        $this->JsonData['status'] = 'success';
                        $this->JsonData['msg'] = 'Login successfull.';
                        $this->JsonData['url'] = route('admin.dashboard');
                    
                    }
                    else
                    {
                        auth()->logout();
                        $this->JsonData['status'] = 'error';
                        $this->JsonData['msg'] = 'This account has deactivated, Please contact website administrator.';
                    }
                }
                else
                {
                    auth()->logout();
                }
            }

            return response()->json($this->JsonData);exit;            
        }

        public function logout()
        {
            self::_setCompanyId();

            if(auth()->user()->hasRole('super-admin')){
                auth()->guard('admin')->logout();
                return redirect('/superadmin');
            }else{
                auth()->guard('admin')->logout();
                return redirect('admin/login');
            }
        }

    /*---------------------------------
    |   FORGOT PASSWORD 
    ------------------------------------------*/

        public function forgotPassword()
        {
            $this->ViewData['moduleTitle']  = 'Forgot Password';
            $this->ViewData['moduleAction'] = 'FORGOT PASSWORD';
            $this->ViewData['modulePath']   = $this->ModulePath.'forgot.password';

            return view($this->ModuleView.'forgot-password', $this->ViewData);
        }
        
        public function forgotPasswordSubmit(ForgotPasswordRequest $request)
        {

            $this->JsonData['status'] = 'error';
            $this->JsonData['msg'] = 'User does not match our records.';

            $email = self::_validateUsername($request->username);
            
            $userCollection = $this->BaseModel->where('email',$email)->first();
             //dd($userCollection);
            
            if (!empty($userCollection)) 
            {
                if (!$userCollection->status) 
                {
                    $this->JsonData['status'] = 'error';
                    $this->JsonData['msg'] = 'User account has disabled. Please contact website administrator.';
                    return response()->json($this->JsonData);exit;
                }


                $userCollection->username = $userCollection->contact_name;
                $token = $this->PasswordBroker->createToken($userCollection);

                $userCollection->url = url('/admin/reset-password/'.$token);
                // dd($userCollection->url);
                
                if($userCollection->company_id>0){

                    $company = self::_getCompanyDetails($userCollection->company_id);
                    $company->company_url = url('/');
                    $company->adminmail = config('constants.ADMINEMAIL');
                    if(!empty($company->logo) && is_file(storage_path().'/app/'.$company->logo)){
                        $company->logo = url('storage/app/'.$company->logo);
                    }else{
                        $company->logo = url('assets/admin/images/logo.jpg');
                    }
                }
                 
                 $userCollection->company = $company;
                // dd($request->all(),$userCollection,$company);

                try {

                    $result = Mail::to($userCollection->email)->send(new ForgotPasswordMail($userCollection,'admin'));

                    $post = $this->PasswordResetModel->create([
                        'email' => $userCollection->email,
                        'token' => $token
                    ]);

                    $this->JsonData['status']   = 'success';
                    $this->JsonData['url']      = route('admin.auth.login');
                    $this->JsonData['msg']      = 'Password reset link send successfully, Please check your email acount.';
                } 
               catch(\Exception $e) {

                    $this->JsonData['exception'] = $e->getMessage();
                    return response()->json($this->JsonData);exit;

                }
            }

            return response()->json($this->JsonData);                    
        }

    /*---------------------------------
    |   RESET PASSWORD
    ------------------------------------------*/
   
        public function resetPassword($token)
        {
            $this->ViewData['moduleTitle']  = 'Reset Password';
            $this->ViewData['moduleAction'] = 'RESET PASSWORD';
            $this->ViewData['modulePath']   = $this->ModulePath.'reset.password';
            
            $collection = $this->PasswordResetModel
                            ->where('token',$token)
                            ->where('created_at','>',Carbon::now()->subHours(24))
                            ->first();

            if(!empty($collection))
            {
                $this->ViewData['email'] = $collection->email; 
                $this->ViewData['token'] = $token;

                return view($this->ModuleView.'.reset-password', $this->ViewData);
            }
            else
            {
                return view($this->ModuleView.'.reset-token-expired', $this->ViewData);
            }
        }

        public function resetPasswordSubmit(ResetPasswordRequest $request, $token)
        {
            $this->JsonData['status'] = 'error';
            $this->JsonData['msg'] = 'Failed to reset password, Token expired';
            
            $isValidObject = $this->PasswordResetModel->where('token',$token)->first();
            if($isValidObject)
            {
                $collection = $this->BaseModel->where('email',$isValidObject->email)->first();
                $this->BaseModel->where('id',$collection->id)->update(['password' => Hash::make($request->password),'str_password'=>$request->password]);
                $this->PasswordResetModel->where('token',$token)->delete();

                $this->JsonData['status']   = 'success';
                $this->JsonData['url']      = route('admin.auth.login');
                $this->JsonData['msg']      = 'Password updated successfully.';
            }

            return response()->json($this->JsonData);
        }
    
    /*---------------------------------
    |   SUBTITUTE FUNCTIONS
    ------------------------------------------*/
        public function _validateUsername($username)
        {
            $email = $username;
            if (!filter_var($username, FILTER_VALIDATE_EMAIL)) 
            {
                $userCollection = $this->BaseModel
                                        ->where('username',  $username)
                                        ->whereStatus(1)
                                        ->first(); 
                // dd($userCollection->getRoleNames());                

                // dd($userCollection->hasRole('super-admin'));
                if(empty($userCollection))
                {   
                    return response()->json($this->JsonData);exit;
                }
                /*if(!empty($userCollection) && !$userCollection->hasRole('super-admin'))
                {   // 
                    return response()->json($this->JsonData);exit;
                }*/
                
                $email = $userCollection->email;
            }

            return $email;
        }

        public function _applyOrDestroyRemember($remember_me, $request)
        {
            if ($remember_me) 
            {
                // removing database  record
                $this->RememberMeModel->where('user_id', auth()->user()->id)
                                        ->delete(); 

                // generating cokie
                $token = time('YmdHisa').auth()->user()->remember_token;
                $minutes = time() + (10 * 365 * 24 * 60 * 60);
                setcookie($this->rememberTitle,$token, $minutes);

                // register remember in database 
                $RememberMeModel = new $this->RememberMeModel;
                $RememberMeModel->user_id = auth()->user()->id;
                $RememberMeModel->username = $request->username;
                $RememberMeModel->password = $request->password;
                $RememberMeModel->remember_token = $token;
                $RememberMeModel->initial_login_date = Date('Y-m-d');
                $RememberMeModel->save();
            }
            else
            {
                if(!empty($_COOKIE[$this->rememberTitle]))
                {
                    // removing cookie
                    $remember_token = $_COOKIE[$this->rememberTitle];
                    setcookie($this->rememberTitle, null, -1);
                    unset($_COOKIE[$this->rememberTitle]);

                    // removing database  record
                    $this->RememberMeModel->where('remember_token', $remember_token)
                                        ->delete();                 
                }
            }  
        }
}
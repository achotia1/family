<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\ForgotPasswordRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;

use App\Mail\ForgotPasswordMail; 

use App\PasswordReset;
use App\Models\StoreUsersModel;
use App\Models\RememberMeModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Hash;
use Mail;
use Cookie;

// temp
use Spatie\Permission\Models\Role;


class LoginController extends Controller
{   
    private $BaseModel;
    private $ViewData;
    private $JsonData;
    private $ModuleTitle;
    private $ModuleView;
    private $ModulePath;

    public function __construct(
       
        StoreUsersModel $StoreUsersModel,
        RememberMeModel $RememberMeModel,
        PasswordReset $PasswordReset
    )
    {

        $this->BaseModel = $StoreUsersModel;   
        $this->RememberMeModel = $RememberMeModel;   
        $this->PasswordReset = $PasswordReset;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Login';
        $this->ModuleView  = 'admin.auth.';
        $this->ModulePath = 'admin.auth.';   
        
        $this->rememberTitle = 'LARAVEL_RSESSION';
    }

    /*---------------------------------
    |   LOGIN
    ------------------------------------------*/

        public function index(Request $request)
        {
            $this->ViewData['moduleTitle']  = $this->ModuleTitle;
            $this->ViewData['moduleAction'] = $this->ModuleTitle;
            $this->ViewData['modulePath']   = $this->ModulePath.'login';
            
            if (!empty($_COOKIE[$this->rememberTitle])) 
            {
                $token = $_COOKIE[$this->rememberTitle];

                $this->ViewData['user'] = $this->RememberMeModel
                                        ->where('remember_token', $token)
                                        ->first();
            }

            return view($this->ModuleView.'login', $this->ViewData);
        }

        public function checkLogin(LoginRequest $request)
        {
            
            $this->JsonData['status'] = 'error';
            $this->JsonData['msg'] = 'Incorrect login details.';

            // check for valid username 
            $credentials = [];
            $credentials['email'] = self::_validateUsername($request->username);
            $credentials['password'] = $request->password;

            $remember_me = !empty($request->remember) ? true : false;
            if (auth()->guard('admin')->attempt($credentials, $remember_me)) 
            {
                self::_applyOrDestroyRemember($remember_me, $request);

                $this->JsonData['status'] = 'success';
                $this->JsonData['msg'] = 'Login successfull.';
                $this->JsonData['url'] = route('admin.dashboard');
            }

            return response()->json($this->JsonData);exit;            
        }

        public function _validateUsername($username)
        {
            $email = $username;
            if (!filter_var($username, FILTER_VALIDATE_EMAIL)) 
            {
                $userCollection = $this->BaseModel->where('username',  $username)->first(); 
                if(empty($userCollection))
                {   
                    return response()->json($this->JsonData);exit;
                }
                if(!empty($userCollection) && !$userCollection->hasRole('admin'))
                {   
                    return response()->json($this->JsonData);exit;
                }
                
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

        public function logout()
        {
            auth()->guard('admin')->logout();
            return redirect('admin');
        }

    /*---------------------------------
    |   FORGOT PASSWORD 
    ------------------------------------------*/

        public function forgotPassword()
        {
            
            return view('admin.auth.forgot-password');
        }
        
        public function forgotPasswordSubmit(ForgotPasswordRequest $request)
        {
            if($request->strEmail)
            {
                $strEmail = $request->strEmail;
                if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) 
                {              
                    $strEmail = $this->BaseModel->where('username',  $strEmail)->value('email');               
                    if(!$strEmail)
                    {
                        return redirect('/admin/forgotPassword')->with('forgotPassword_error','User does not exist');
                    }                    
                }
                
                $arrAdminData = $this->BaseModel->where('email',$strEmail)->first();

                if (empty($arrAdminData) && sizeof($arrAdminData) == 0) 
                {
                   return redirect('/admin/forgotPassword')->with('forgotPassword_error','User does not exist');
                }
                else
                {
                    $intId      = $arrAdminData->id;
                    $strEmail   = $arrAdminData->email;
                    $strName    = $arrAdminData->username;                            
                    
                    $data       = [];
                    $data['name'] = $strName;

                    $data['url'] = url('/admin/reset-password/'.base64_encode(base64_encode($intId))); 

                    try {
                         $result = Mail::to($strEmail)->send(new ForgotPasswordMail($data,'admin')); 
                            $post = $this->PasswordReset->create([
                            'email' => $strEmail,
                            'token' => base64_encode(base64_encode($intId))
                        ]);
                    } 
                    catch (\Throwable $th) 
                    {
                        return redirect('/admin/forgotPassword')->with('forgotPassword_error','Error while sending mail');
                    }
                    
                    return redirect('/admin')->with('login_success','Please check your email for updating password');
                }            
            }
            else 
            {
                  return redirect('/admin/forgotPassword')->with('forgotPassword_error','Enter email address');
            }
        }

    /*---------------------------------
    |   RESET PASSWORD
    ------------------------------------------*/
   
        public function resetPassword($intToken)
        {
            
            $arrData = $this->PasswordReset->where('token',$intToken)->first();
            if(!empty($arrData))
            {
                $strEmail = $arrData->email;
                $arrUserData['arrUserData'] = $this->BaseModel->where('email',$strEmail)->first();
                $arrUserData['arrToken'] = $intToken;           
                return view('admin.auth.reset-password',$arrUserData);

            }
            else
            {
                return redirect('/admin/forgot-password')->with('forgotPassword_error','Invalid Token');
            }
        }

        public function resetPasswordSubmit(ResetPasswordRequest $request)
        {
            $strNewPassword  = $request->strPassword;
            $strComPassword  = $request->strCPassword;
            $strToken        = $request->hiddenToken;

            //check token exists in db
            $arrChkData = $this->PasswordReset->where('token',$strToken)->first();
            
            if($arrChkData)
            {
                $arrUserData = $this->BaseModel->where('email',$arrChkData->email)->first();
                
                $intUserId   = $arrUserData->id;
                $arrData = array(
                    'password'       => Hash::make($strNewPassword),
                    'updated_at'     =>date('Y-m-d H:i:s')
                );

                $this->BaseModel->where('id',$intUserId)->update($arrData);

                $this->PasswordReset->where('token',$strToken)->delete();

                return redirect('/admin/login')->with('login_sucess','Password updated successfully');
                
            }
            else
            {
                return redirect('/admin/login')->with('login_error','Error while updating password');
            }
        }
    
}
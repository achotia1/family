<?php

namespace App\Http\Middleware\Admin;

use Closure;

use Session;

// Models
use Spatie\Permission\Models\Role;
use App\Models\CompanyModel;
//Trait
use App\Traits\GeneralTrait;


class Authenticate
{   
    use GeneralTrait;

    public function __construct(Role $RoleModel,CompanyModel $CompanyModel)
    {
        $this->RoleModel  = $RoleModel;
        $this->CompanyModel  = $CompanyModel;
    }

    public function handle($request, Closure $next)
    {
        // self::_setCompanyId();
            // auth()->guard('admin')->logout();
            //  return redirect('admin/login');
            // exit;
            // dd(auth()->user()->roles);
        if(auth()->check())
        {
            if (auth()->user()->roles->pluck('guard_name')->first() === 'admin') 
            {

                if (auth()->user()->status) 
                {
                   $allRoles = $this->RoleModel
                                ->where('guard_name', 'admin')
                                ->where('name', '!=', 'super-admin')
                                ->orderBy('name', 'ASC')
                                ->get();

                     if(auth()->user()->hasRole('super-admin')){           
                        $company_id = self::_getCompanyId();
                          
                    }else{
                        $company_id = auth()->user()->company_id;
                        self::_setCompanyId($company_id);
                       
                    }
                    // dd($company_id);
                    $company = $this->CompanyModel
                                ->where('id', $company_id)
                                ->whereStatus(1)
                                ->first();
                     // dump($company,$company_id);
                     if($company){
                        view()->share(['roles'=> $allRoles,'company'=> $company]);
                        return $next($request);
                    }else{
                        auth()->logout();
                        self::_setCompanyId('');
                        return redirect('/admin/login')->with('message','Your Company account is deactivated. Please contact to website Administrator');;
                    }
                    //view()->share('roles', $allRoles);
                    // dd($company);     
                    return $next($request);      
                }
                else
                {
                    auth()->logout();
                    return redirect('/admin/login');
                }

            }
            else
            {
                auth()->logout();
                return redirect('/admin/login');
            }
        }
        else
        {
            return redirect('/admin/login');
        }
    }
}

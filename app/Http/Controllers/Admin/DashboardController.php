<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

//Models
use App\Models\StoreUsersModel;
use App\Models\StoreRawMaterialModel;
// use App\Models\OrdersModel;
use App\Models\CompanyModel;
use App\Traits\GeneralTrait;


use Validator;

class DashboardController extends Controller
{
    use GeneralTrait;

    public function __construct(
                                StoreUsersModel $StoreUsersModel,
                                StoreRawMaterialModel $StoreRawMaterialModel,
                                CompanyModel $CompanyModel
                                // OrdersModel $OrdersModel,
                            )
    {
        $this->ViewData = [];
        $this->JsonData = [];
        $this->todosByDate= [];
        
        $this->CompanyModel     = $CompanyModel;
        $this->StoreUsersModel   = $StoreUsersModel;
        // $this->OrdersModel      = $OrdersModel;
        $this->StoreRawMaterialModel    = $StoreRawMaterialModel;

        $this->ModuleTitle  = 'Dashboard';
        $this->ModuleView   = 'admin.dashboard.';
        $this->ModulePath   = 'admin.dashboard';
       
    }

    public function index()
    {


        $this->ViewData['moduleTitle']  = $this->ModuleTitle;
        $this->ViewData['moduleAction'] = $this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        $company_id = self::_getCompanyId();
        // dd($company_id);
        // dd(auth());
        // self::_getAuthenticationForToken();
        //$company_id = auth()->user()->company_id;
        // dd($company);

        $usersCount =  $this->StoreUsersModel
                                /*->whereHas('roles', function($query) {
                                    $query->where('name', '=','customer');
                                })*/
                             ->where('store_users.company_id',$company_id)
                             ->Orwhere('store_users.company_id',0)
                             ->whereStatus(1)
                             ->count();
        // dd($usersCount);
        
        $rawMaterialsCount =  $this->StoreRawMaterialModel
                                 ->where('store_raw_materials.company_id', $company_id)
                                 ->count();
                                 // ->where('products.company_id', $company_id)
        
        $count['customer']          = $usersCount;
        $count['rawMaterial']           = $rawMaterialsCount;
        // $count['completedOrder']    = $completedOrderCount;
        // $count['totalOrder']        = $totalOrderCount;
        // $count['pendingOrder']      = $pendingOrderCount;
        // $count['dispatchOrder']     = $dispatchOrderCount;
        // $count['confirmedOrder']     = $confirmedOrderCount;

        $this->ViewData['count'] = $count;

        /*$count['customer']          = 0;
        $count['product']           = 0;
        $count['completedOrder']    = 0;
        $count['totalOrder']        = 0;
        $count['pendingOrder']      = 0;
        $count['dispatchOrder']     = 0;
        $count['confirmedOrder']    = 0;*/

        $this->ViewData['count'] = $count;
        //dd($this->ViewData);                                                                    
        return view($this->ModuleView.'index', $this->ViewData);
    }




    
}
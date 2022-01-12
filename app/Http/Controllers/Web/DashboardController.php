<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;



use Validator;

class DashboardController extends Controller
{
    //use GeneralTrait;

    public function __construct(
                              
                            )
    {
        $this->ViewData = [];
        $this->JsonData = [];
        $this->todosByDate= [];
        
     
         $this->ModuleView   = 'web.users.';
       
    }

    public function index()
    {
        
        return view($this->ModuleView.'create');
dd('test');

        $this->ViewData['moduleTitle']  = $this->ModuleTitle;
        $this->ViewData['moduleAction'] = $this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        $company_id = self::_getCompanyId();
        // dd($company_id);
        // dd(auth());
        // self::_getAuthenticationForToken();
        //$company_id = auth()->user()->company_id;
        // dd($company);

        

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
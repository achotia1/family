<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\StoreUsersModel;
use App\Models\RoleHasPermissionsModel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Http\Requests\Admin\PermissionsRequest;

class PermissionsController extends Controller
{
    private $BaseModel;
    private $ViewData;
    private $JsonData;
    private $ModuleTitle;
    private $ModuleView;
    private $ModulePath;

    public function __construct(
        StoreUsersModel $StoreUsersModel,
        Role $RoleModel,
        Permission $Permission,
        RoleHasPermissionsModel $RoleHasPermissionsModel
    )
    {
        $this->BaseModel    = $Permission;
        $this->RoleModel    = $RoleModel;
        $this->RoleHasPermissionsModel = $RoleHasPermissionsModel;

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Permissions';
        $this->ModuleView  = 'admin.permissions.';
        $this->ModulePath = 'admin.permissions';
    }
    
    public function index()
    {
        // Default site settings
        $this->ViewData['moduleTitle']  = 'Manage '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Manage '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePath;

        // All userdata
        $this->ViewData['users'] = $this->BaseModel->orderBy('id', 'DESC')->get();
        // dd($this->ViewData);
        // view file with data
        return view($this->ModuleView.'index', $this->ViewData);
    }

    public function byRole(Request $request)
    {
        $id = base64_decode(base64_decode($request->id));

        $this->JsonData['object'] = $this->RoleHasPermissionsModel
                                        ->with(['permissions'])
                                        ->where('role_id', $id)
                                        ->get(['permission_id']);   
        
        return response()->json($this->JsonData);
    }   

    public function getRole(Request $request)
    {
        $options = '';

        $rolesCollection = $this->RoleModel->where('name', '!=', 'super-admin')->orderBy('name', 'ASC')->get();
        if(!empty($rolesCollection) && sizeof($rolesCollection))
        {
            $options = '<option value="" >Please select</option>';
            
            foreach ($rolesCollection as $key => $value) 
            {
                $options.= '<option value="'.base64_encode(base64_encode($value->id)).'" >'.ucfirst($value->name).'</option>';
            }

        }

        echo $options;
    } 
   
    public function create()
    {
        //
    }

    public function store(PermissionsRequest $request)
    {
        // dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to update permissions, Something went wrong on server.';

        $role_id  = base64_decode(base64_decode($request->role));
        $role     = $this->RoleModel->find($role_id);
        $role->syncPermissions($request->except(['role']));

        if ($role) 
        {
            $this->JsonData['status'] = 'success';
            $this->JsonData['msg'] = 'Permissions updated successfully.';
        }

        // flush permission cache
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json($this->JsonData);

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}

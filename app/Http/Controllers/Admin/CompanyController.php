<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\CompanyModel;

use App\Http\Requests\Admin\CompanyRequest;

use Storage;

class CompanyController extends Controller
{   
    private $BaseModel;
    
    public function __construct(

        CompanyModel $CompanyModel
    )
    {

        $this->BaseModel = $CompanyModel;   

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleView   = 'admin.companies.';
        $this->ModulePath   = 'admin.companies.';   

        $this->ModuleTitle = "Company";
        
        $this->ModulePathAdmin   = 'admin.company.';   
        
    }

    /*---------------------------------
    |   Comnpanies Screens
    ------------------------------------------*/

    public function frontindex(Request $request)
    {
        $this->ViewData['moduleTitle']  = 'Companies List';
        $this->ViewData['moduleAction'] = 'Companies List';
        $this->ViewData['modulePath']   = $this->ModulePath.'index';

        $this->ViewData['companies']   = $this->BaseModel->whereStatus(1)->get();
            // dd($this->ViewData['companies']);
        return view($this->ModuleView.'front-index', $this->ViewData);
    }

    public function index()
    {

        $this->ViewData['moduleTitle']  = 'Companies List';
        $this->ViewData['moduleAction'] = 'Companies List';
        $this->ViewData['modulePath']   = $this->ModulePathAdmin.'create';

            // $this->ViewData['companies']   = $this->BaseModel->whereStatus(1)->get();
            // dd($this->ViewData['companies']);
        return view($this->ModuleView.'index', $this->ViewData);
    }

    public function create()
    {
            // Default site settings
        $this->ViewData['moduleTitle']  = 'Add New '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Add New '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePathAdmin;

            // view file with data
        return view($this->ModuleView.'create', $this->ViewData);
    }

    public function edit($encID)
    {
            // Default site settings
        $this->ViewData['moduleTitle']  = 'Edit '.$this->ModuleTitle;
        $this->ViewData['moduleAction'] = 'Edit '.$this->ModuleTitle;
        $this->ViewData['modulePath']   = $this->ModulePathAdmin;

            // All data
        $this->ViewData['companydata'] = $this->BaseModel->find(base64_decode(base64_decode($encID)));

            // view file with data
        return view($this->ModuleView.'edit', $this->ViewData);
    }

    public function update(CompanyRequest $request, $encID)
    {
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to update company, Something went wrong on server.';       

        $id = base64_decode(base64_decode($encID));
        try {

            $collection = $this->BaseModel->find($id);   

            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route($this->ModulePathAdmin.'index');
                $this->JsonData['msg'] = 'Company Updated successfully.'; 
            }
        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);

    }

    public function store(CompanyRequest $request)
    {
            // dd($request->all(),'store');
        $this->JsonData['status'] = __('admin.RESP_ERROR');
        $this->JsonData['msg'] = 'Failed to create company, Something went wrong on server.'; 

        try {

            $collection = new $this->BaseModel;   
            $request->add = 1;
            $collection = self::_storeOrUpdate($collection,$request);

            if($collection){
                $this->JsonData['status'] = __('admin.RESP_SUCCESS');
                $this->JsonData['url'] = route($this->ModulePathAdmin.'index');
                $this->JsonData['msg'] = 'Company created successfully.'; 
            }

        }
        catch(\Exception $e) {

            $this->JsonData['msg'] = $e->getMessage();
        }

        return response()->json($this->JsonData);
    }

    public function _storeOrUpdate($collection, $request)
    {
        $collection->name              = $request->name;

        if (!empty($request->logo)) 
        {
            $path = 'companies';

            $objDocument = $request->logo;

            $original_file  = strtolower($objDocument->getClientOriginalName());
            $extension      = strtolower($objDocument->getClientOriginalExtension());

            $filename       = date('YmdHis').'-'.$original_file;
            $file           = Storage::putFileAs($path, $objDocument, $filename);

            $collection->logo = $file;
            $collection->logo_original_img = $original_file;

            if(is_file(storage_path().'/app/'.$request->old_image_data))
            {
                unlink(storage_path().'/app/'.$request->old_image_data);
            }
        }

        if($request->add==1){
            $company_id = $this->BaseModel->orderBy('id','DESC')->limit(1)->first(['id']);
            // dd($company_id->id);
            $collection->company_url   = $request->company_url."/".base64_encode($company_id->id+1);
        }else{
            $collection->company_url   = $request->company_url;
        }
            // $collection->status    =  1;//Active
        $collection->status     = !empty($request->status) ? 1 : 2;
            //Save data
        $collection->save();

        return $collection;
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
                0 => 'companies.id',
                1 => 'companies.id',
                2 => 'companies.name',
                3 => 'companies.company_url',
                4 => 'companies.status',
            );

            /*--------------------------------------
            |  Model query and filter
            ------------------------------*/

                // start model query
            $modelQuery =  $this->BaseModel->whereIn('status',[1,2]);

                // get total count 
            $countQuery = clone($modelQuery);            
            $totalData  = $countQuery->count();

                // filter options
            $custom_search = false;
            if (!empty($request->custom))
            {

                if (!empty($request->custom['name'])) 
                {
                    $custom_search = true;

                    $key = $request->custom['name'];

                    $modelQuery = $modelQuery
                    ->where('companies.name', 'LIKE', '%'.$key.'%');

                }

                    /*if (!empty($request->custom['code'])) 
                    {
                        $custom_search = true;

                        $key = $request->custom['code'];

                        $modelQuery = $modelQuery
                                    ->where('companies.code', 'LIKE', '%'.$key.'%');
                        
                                }*/

                            }

                // get total filtered
                            $filteredQuery = clone($modelQuery);            
                            $totalFiltered  = $filteredQuery->count();

                // offset and limit
                            if(empty($column))
                            {   
                                $modelQuery = $modelQuery->orderBy('companies.status', 'ASC');           
                    //$modelQuery = $modelQuery->orderBy('companies.name', 'ASC');           
                            }
                            else
                            {
                                $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
                            }

                            $object = $modelQuery->skip($start)
                            ->take($length)
                            ->get(['companies.id', 
                                'companies.name', 
                                'companies.company_url', 
                                'companies.status', 
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

                    $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="company[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                    $data[$key]['name']  = '<span title="'.ucfirst($row->name).'">'.str_limit(ucfirst($row->name), '60', '...').'</span>';
                    $company_url = strtolower($row->company_url);
                    $data[$key]['company_url']  = "<a href='".$company_url."' target='_blank'>".$company_url."</a>";
                    $data[$key]['code']  =  $row->code;

                    if (!empty($row->status)) 
                    {
                        if($row->status==1){
                            $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                        }elseif ($row->status==2) {
                           $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Inactive</span>';
                       }

                   }
                        //$view = '<a href="'.route($this->ModulePath.'show',[ base64_encode(base64_encode($row->id))]).'"><img src="'.url('/assets/admin/images').'/icons/eye.svg" alt=" view"></a>';
                        //'.route($this->ModulePathAdmin.'edit', [ base64_encode(base64_encode($row->id))]).'
                   $edit = '<a href="'.route($this->ModulePathAdmin.'edit', [ base64_encode(base64_encode($row->id))]).'"><img src="'.url('/assets/admin/images').'/icons/edit.svg" alt=" edit"></a>';

                        //$data[$key]['actions'] = '<div class="text-center">'.$view.'</div>';

                        //if(auth()->user()->can('manage-bundles'))
                        //{
                   $data[$key]['actions'] =  '<div class="text-center">'.$edit.'</div>';
                        //}

               }
           }

                // search html
           $searchHTML['id']       =  '';
           $searchHTML['select']   =  '';
           $searchHTML['name']     =  '<input type="text" class="form-control" id="product-name" value="'.($request->custom['name']).'" placeholder="Search...">';
                //$searchHTML['code']     =  '<input type="text" class="form-control" id="product-code" value="'.($request->custom['code']).'" placeholder="Search...">';
           $searchHTML['company_url']   =  '';
           $searchHTML['status']   =  '';
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

    public function bulkDelete(Request $request)
    {
            // dd($request->all());
        $this->JsonData['status'] = 'error';
        $this->JsonData['msg'] = 'Failed to delete companies, Something went wrong on server.';

        if (!empty($request->arrEncId)) 
        {
            $arrID = array_map(function($item)
            {
                return base64_decode(base64_decode($item));

            }, $request->arrEncId);

            try 
            {

                if ($this->BaseModel->whereIn('id', $arrID)->update(['status'=>0])) 
                {
                    $this->JsonData['status'] = 'success';
                    $this->JsonData['msg'] = 'Companies deleted successfully.';
                }

            } catch (Exception $e) 
            {
             $this->JsonData['exception'] = $e->getMessage();
         }
     }

     return response()->json($this->JsonData);   
 }



}
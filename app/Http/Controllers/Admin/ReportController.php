<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StoreOutMaterialModel;
use App\Models\StoreBatchCardModel;
use App\Models\ProductsModel;

use App\Traits\GeneralTrait;

class ReportController extends Controller
{   
    private $BaseModel;
    use GeneralTrait;
    
    public function __construct(

        StoreOutMaterialModel $StoreOutMaterialModel
    )
    {

        $this->BaseModel = $StoreOutMaterialModel;   

        $this->ViewData = [];
        $this->JsonData = [];

        $this->ModuleTitle = 'Reports';
        $this->ModuleView  = 'admin.reports.';
        $this->ModulePath  = 'admin.reports.';
        
    }

    public function batchIndex()
    {
        ## DEFAULT SITE SETTINGS
        $this->ViewData['moduleTitle']  = 'Batch-Wise Report';
        $this->ViewData['moduleAction'] = 'Batch-Wise Report';
        $this->ViewData['modulePath']   = $this->ModulePath;        

        // view file with data
        return view($this->ModuleView.'batches',$this->ViewData);
    }

    public function getBatchRecords(Request $request)
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
            0 => 'store_out_materials.id',
            1 => 'store_out_materials.id',
            2 => 'store_batch_cards.batch_card_no',
            3 => 'products.code',            
            4 => 'store_out_materials.sellable_qty',
            5 => 'store_out_materials.loss_material',
            6 => 'store_out_materials.yield',                    
        );

        /*--------------------------------------
        |  MODEL QUERY AND FILTER
        ------------------------------*/

        ## START MODEL QUERY         
        $companyId = self::_getCompanyId();
        $modelQuery =  $this->BaseModel        
        ->selectRaw('store_out_materials.id, store_out_materials.plan_id, store_out_materials.sellable_qty, store_out_materials.loss_material, store_out_materials.yield, store_productions.batch_id, store_batch_cards.batch_card_no, products.name, products.code')
        ->leftjoin('store_productions', 'store_productions.id' , '=', 'store_out_materials.plan_id')
        ->leftjoin('store_batch_cards', 'store_batch_cards.id' , '=', 'store_productions.batch_id')
        ->leftjoin('products', 'products.id' , '=', 'store_batch_cards.product_code')
        ->where('store_out_materials.company_id', $companyId)
        ->where('store_productions.deleted_at', null);
        //dd($modelQuery->toSql());
        ## GET TOTAL COUNT
        $countQuery = clone($modelQuery);            
        $totalData  = $countQuery->count();

        ## FILTER OPTIONS
        $custom_search = false;
        if (!empty($request->custom))
        {            
           
            if (!empty($request->custom['from-date']) && !empty($request->custom['to-date'])) 
            {
                $custom_search = true;

                $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                $start_date   = date_format($dateObject, 'Y-m-d'); 

                $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                $end_date   = date_format($dateObject, 'Y-m-d'); 

                if (strtotime($start_date)==strtotime($end_date)){
                    
                    $modelQuery  = $modelQuery
                                        ->whereDate('store_batch_cards.created_at','=',$start_date);

                }else{
                    $modelQuery = $modelQuery
                                    ->whereBetween('store_batch_cards.created_at', 
                                    array($start_date,$end_date));
                }

            

            }else if(!empty($request->custom['from-date']) && empty($request->custom['to-date'])) 
            {

                $dateObject = date_create_from_format("d-m-Y",$request->custom['from-date']);
                $start_date   = date_format($dateObject, 'Y-m-d'); 

                $modelQuery = $modelQuery
                ->whereDate('store_batch_cards.created_at','>=',$start_date);

            }else if(empty($request->custom['from-date']) && !empty($request->custom['to-date'])) 
            {

                $dateObject = date_create_from_format("d-m-Y",$request->custom['to-date']);
                $end_date   = date_format($dateObject, 'Y-m-d'); 

                $modelQuery = $modelQuery
                ->whereDate('store_batch_cards.created_at','<=',$end_date);
            }         
            
        }

        /*if (!empty($request->search))
        {
            if (!empty($request->search['value'])) 
            {
                $search = $request->search['value'];

                 $modelQuery = $modelQuery->where(function ($query) use($search)
                {
                    $query->orwhere('store_batch_cards.batch_card_no', 'LIKE', '%'.$search.'%');   
                    $query->orwhere('products.name', 'LIKE', '%'.$search.'%');
                    $query->orwhere('products.code', 'LIKE', '%'.$search.'%'); 
                    $query->orwhere('store_out_materials.sellable_qty', 'LIKE', '%'.$search.'%');
                    $query->orwhere('store_out_materials.loss_material', 'LIKE', '%'.$search.'%');
                    $query->orwhere('store_out_materials.yield', 'LIKE', '%'.$search.'%');
                });              

            }
        }*/

        ## GET TOTAL FILTER
        $filteredQuery = clone($modelQuery);            
        $totalFiltered  = $filteredQuery->count();

        ## OFFSET AND LIMIT
        if(empty($column))
        {   
            $modelQuery = $modelQuery->orderBy('store_out_materials.id', 'DESC');
                        
        }
        else
        {
            $modelQuery =  $modelQuery->orderBy($filter[$column], $dir);
        }
        //dd($modelQuery->toSql());
        $object = $modelQuery->skip($start)
        ->take($length)
        ->get();  


        /*--------------------------------------
        |  DATA BINDING
        ------------------------------*/

        $data = [];

        if (!empty($object) && sizeof($object) > 0)
        {
            foreach ($object as $key => $row)
            {

                $data[$key]['id'] = $row->id;

               // $data[$key]['select'] = '<label class="checkbox-container d-inline-block"><input type="checkbox" name="store_in_materials[]" value="'.base64_encode(base64_encode($row->id)).'" class="rowSelect"><span class="checkmark"></span></label>';

                $data[$key]['batch_id']     = "<a href=".route('admin.report.showBatch',[ base64_encode(base64_encode($row->id))]).">".$row->batch_card_no.'</a>';
                $data[$key]['product_code'] =  $row->name;
                $data[$key]['sellable_qty'] =  $row->sellable_qty;
                $data[$key]['loss_material']=  number_format($row->loss_material, 2, '.', '');
                $data[$key]['yield']  =  number_format($row->yield, 2, '.', '');          

                // if($row->status==1){
                //     $data[$key]['status'] = '<span class="theme-green semibold text-center f-18">Active</span>';
                // }elseif($row->status==0) {
                //  $data[$key]['status'] = '<span class="theme-gray semibold text-center f-18">Closed</span>';
                // }
                
               //$data[$key]['actions'] =  '<div class="text-center"></div>';
                

        }
    }

    /*$objStore = new StoreBatchCardModel;
    $batchNos = $objStore->getBatchNumbers();
    $batch_no_string = '<select name="batch_id" id="batch-id" class="form-control my-select"><option class="theme-black blue-select" value="">Select Batch</option>';
        foreach ($batchNos as $val) {
            $batch_no_string .='<option class="theme-black blue-select" value="'.$val['id'].'" '.( $request->custom['batch_id'] == $val['id'] ? 'selected' : '').' >'.$val['batch_card_no'].'</option>';
        }
    $batch_no_string .='</select>';
    $objProduct = new ProductsModel;
    $products = $objProduct->getProducts($companyId);
    $product_code_string = '<select name="product_code" id="product-code" class="form-control my-select"><option class="theme-black blue-select" value="">Select Product</option>';
        foreach ($products as $product) {
            $product_code_string .='<option class="theme-black blue-select" value="'.$product['id'].'" '.( $request->custom['product_code'] == $product['id'] ? 'selected' : '').' >'.$product['code'].' ('.$product['name'].' )</option>';
        }
    $product_code_string .='</select>';
    ## SEARCH HTML
    $searchHTML['id']       =  '';
    $searchHTML['select']   =  '';
    $searchHTML['batch_id']     =  $batch_no_string;
    $searchHTML['product_code']     =  $product_code_string;
    $searchHTML['sellable_qty']   =  '<input type="text" class="form-control" id="sellable-qty" value="'.($request->custom['sellable_qty']).'" placeholder="Search...">';
    $searchHTML['loss_material']   =  '<input type="text" class="form-control" id="loss-material" value="'.($request->custom['loss_material']).'" placeholder="Search...">';
    $searchHTML['yield']   =  '<input type="text" class="form-control" id="yield" value="'.($request->custom['yield']).'" placeholder="Search...">';*/
    //$searchHTML['status']   =  '';  
    /*$searchHTML['status']   =  '<select name="status" id="search-status" class="form-control my-select">
            <option class="theme-black blue-select" value="">Status</option>
            <option class="theme-black blue-select" value="1" '.( $request->custom['status'] == "1" ? 'selected' : '').' >Active</option>
            <option class="theme-black blue-select" value="0" '.( $request->custom['status'] == "0" ? 'selected' : '').'>Closed</option>            
            </select>';
    // $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';removeSearch

    if ($custom_search) 
    {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return removeSearch(this)" class="btn btn-danger"><span class="fa  fa-remove"></span></a></div>';
    }
    else
    {
        $seachAction  =  '<div class="text-center"><a style="cursor:pointer;" onclick="return doSearch(this)" class="btn btn-primary"><span class="fa  fa-search"></span></a></div>';
    }

    $searchHTML['actions'] = $seachAction;


    array_unshift($data, $searchHTML);*/

    ## WRAPPING UP
    $this->JsonData['draw']             = intval($request->draw);
    $this->JsonData['recordsTotal']     = intval($totalData);
    $this->JsonData['recordsFiltered']  = intval($totalFiltered);
    $this->JsonData['data']             = $data;

    return response()->json($this->JsonData);
}

}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductsModel;
use App\Models\StoreProductOpeningModel;

use DB;
use Carbon\Carbon;
class AddProductStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stockbalance:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Insert opening balances in store_product_openings daily @12:15 AM at night every day. Previous day's closing balance will be opening balance for current day.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
                                ProductsModel $ProductsModel,
                                StoreProductOpeningModel $StoreProductOpeningModel
                                )
    {
        parent::__construct();
        $this->BaseModel  = $ProductsModel;
        $this->StoreProductOpeningModel  = $StoreProductOpeningModel;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$todaysDate =  Carbon::today()->format('Y-m-d');
        $sub= DB::raw('(SELECT deleted_at, product_id, 
                      SUM(store_sales_stock.balance_quantity) as total_balance 
                      FROM store_sales_stock
                      WHERE deleted_at is null
                      GROUP BY product_id) ps');
        
        $objProduct = new ProductsModel;
        $modelQuery =  $objProduct
       ->selectRaw('
                    products.id,
                    products.name,
                    products.code,                    
                    products.status,
                    IFNULL(ps.total_balance, 0) AS total_balance 
                    ')        
        ->leftjoin($sub,function($join){
            $join->on('ps.product_id','=','products.id');
        })
        
        ->orderBy('products.id', 'ASC');
        $object = $modelQuery->get();
        $dataArr = array();
        $i = 0;
        if(!empty($object)){
            foreach($object as $key=>$valData){
                $createdDate = $updateddate = Carbon::now()->toDateTimeString();
                $dataArr[$i]['product_id'] = $valData->id; 
                $dataArr[$i]['opening_bal'] = $valData->total_balance;
                $dataArr[$i]['opening_date'] = $todaysDate;
                $dataArr[$i]['created_at'] = $createdDate;
                $dataArr[$i]['updated_at'] = $updateddate;
                $i++;
            }
        }
        if(!empty($dataArr)){            
            $openingObj = new StoreProductOpeningModel;
            $openingObj->insert($dataArr);            
        }
    }
}

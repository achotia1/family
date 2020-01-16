<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreRawMaterialModel;
use App\Models\StoreMaterialOpeningModel;

use DB;
use Carbon\Carbon;
class AddOpeningStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openingbalance:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Insert opening balances in store_material_openings daily @12:05 AM at night every day. Previous day's closing balance will be opening balance for current day.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
                                StoreRawMaterialModel $StoreRawMaterialModel,
                                StoreMaterialOpeningModel $StoreMaterialOpeningModel
                                )
    {
        parent::__construct();
        $this->BaseModel  = $StoreRawMaterialModel;
        $this->StoreMaterialOpeningModel  = $StoreMaterialOpeningModel;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$todaysDate =  Carbon::today()->format('Y-m-d');
    	//dd($todaysDate);
        //$todaysDate = '2020-01-17';        
        $sub= DB::raw('(SELECT deleted_at, material_id, 
                      SUM(store_in_materials.lot_balance) as total_balance 
                      FROM store_in_materials
                      WHERE deleted_at is null
                      GROUP BY material_id) im');
        
        $modelQuery =  $this->BaseModel
       ->selectRaw('
                    store_raw_materials.id,
                    store_raw_materials.name,
                    store_raw_materials.moq,
                    store_raw_materials.unit, 
                    store_raw_materials.material_type,
                    store_raw_materials.status,
                    IFNULL(im.total_balance, 0) AS total_balance 
                    ')        
        ->leftjoin($sub,function($join){
            $join->on('im.material_id','=','store_raw_materials.id');
        })        
        ->orderBy('store_raw_materials.id', 'ASC');
        $object = $modelQuery->get();
        $dataArr = array();
        $i = 0;
        if(!empty($object)){
            foreach($object as $key=>$valData){
                $dataArr[$i]['material_id'] = $valData->id; 
                $dataArr[$i]['opening_bal'] = $valData->total_balance;
                $dataArr[$i]['opening_date'] = $todaysDate;
                $i++;
            }
        }
        //$jasonData = json_encode($dataArr);
        //dd($jasonData);
        if(!empty($dataArr)){            
            $this->StoreMaterialOpeningModel->insert($dataArr);            
        }
    }
}

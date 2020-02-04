<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreWasteStockModel;
use App\Models\StoreWastageOpeningModel;

use DB;
use Carbon\Carbon;
class AddOpeningWstocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wastagebalance:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Insert wastage opening balances in store_wastage_openings daily @12:30 AM at night every day. Previous day's closing balance will be opening balance for current day.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
                                StoreWasteStockModel $StoreWasteStockModel,
                                StoreWastageOpeningModel $StoreWastageOpeningModel
                                )
    {
        parent::__construct();
        $this->BaseModel  = $StoreWasteStockModel;
        $this->StoreWastageOpeningModel  = $StoreWastageOpeningModel;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$todaysDate =  Carbon::today()->format('Y-m-d');
        $wastageResult =  $this->BaseModel
        ->selectRaw('
                    company_id,
                    IFNULL(SUM(balance_course), 0) AS balance_course,
                    IFNULL(SUM(balance_rejection), 0) AS balance_rejection,                    
                    IFNULL(SUM(balance_dust), 0) AS balance_dust,
                    IFNULL(SUM(balance_loose), 0) AS balance_loose
                    ')
        ->where('deleted_at',null)
        ->groupBy('company_id')
        ->orderBy('company_id', 'ASC')
        ->get();
        $dataArr = array();
        $i = 0;
        if(!empty($wastageResult)){
            foreach($wastageResult as $key=>$valData){
                $dataArr[$i]['company_id'] = $valData->company_id; 
                $dataArr[$i]['balance_course'] = $valData->balance_course;
                $dataArr[$i]['balance_rejection'] = $valData->balance_rejection;
                $dataArr[$i]['balance_dust'] = $valData->balance_dust;
                $dataArr[$i]['balance_loose'] = $valData->balance_loose;
                $dataArr[$i]['opening_date'] = $todaysDate;
                $i++;
            }
        }
        if(!empty($dataArr)){            
            //$objWastageOpening = new StoreWastageOpeningModel;
            $this->StoreWastageOpeningModel->insert($dataArr);            
        }
    }
}

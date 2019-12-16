<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreRawMaterialModel;
use App\Models\CompanyModel;

use App\Mail\MoqNotificationMail;

use DB;
use Mail;
class MoqNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moq:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily twice email in a day.i.e at 9:00 am and 4:00 pm to admin, when balance qty is less than moq value';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
                                StoreRawMaterialModel $StoreRawMaterialModel,
                                CompanyModel $CompanyModel
                                )
    {
        parent::__construct();
        $this->BaseModel  = $StoreRawMaterialModel;
        $this->CompanyModel  = $CompanyModel;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = $this->CompanyModel->whereStatus(1)->get();
        $cmpids = [];
        if(!empty($companies)){
            $cmpids =array_column($companies->toArray(), "id");
        }
        $cmpids_string = implode(",", $cmpids);
        // dd($cmpids,$cmpids_string);
        
        $sqlQuery="SELECT
                companies.name as companyName,
                companies.id as companyId,
                store_raw_materials.id,
                store_raw_materials.name,
                store_raw_materials.moq,
                store_raw_materials.unit,
                store_raw_materials.material_type,
                store_raw_materials.status,
                SUM(store_in_materials.lot_balance) AS total_balance
            FROM
                `store_raw_materials`
            LEFT JOIN `store_in_materials` ON 
                `store_in_materials`.`material_id` = `store_raw_materials`.`id`
            JOIN `companies` ON 
                `companies`.`id` = `store_raw_materials`.`company_id`
            WHERE
                `store_raw_materials`.`company_id` IN (".$cmpids_string.") AND `store_in_materials`.`deleted_at` IS NULL AND `store_raw_materials`.`deleted_at` IS NULL
            GROUP BY
                `store_in_materials`.`material_id`
            HAVING
                `total_balance` < store_raw_materials.moq";
        $collection = collect(DB::select(DB::raw($sqlQuery)));
        // dd($collection);

        if(!empty($collection)){
            $mail_collection = [];
            foreach ($collection as $key=>$value) {
               $mail_collection[$value->companyName][$key]=$value;
            }

            // dd($mail_collection);
            $result = Mail::to(config('constants.ADMINEMAIL'))->send(new MoqNotificationMail($mail_collection));
        }
    }
}

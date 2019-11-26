<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StoreBatchCardModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_batch_cards';

    protected $fillable = [
		'product_code',
        'batch_card_no',
        'batch_qty',
        'status',
        'review_status',
        'plan_added'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /*public function assignedUserProducts()
    {
    	return $this->hasMany(UserHasProductsModel::class, 'product_id', 'id');
    }*/

    public function assignedProduct()
    {
        return $this->belongsTo(ProductsModel::class, 'product_code', 'id');
    }

    public function hasProduction()
    {       
        return $this->hasOne(StoreProductionModel::class, 'batch_id', 'id');
    }

    public function getBatchCardNo() {
        $todaysRecords = self::whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->first();
        $cardNoArr[0] = 0;
        $numRecord = sprintf("%02d", 1);
        if(!empty($todaysRecords)){
            $cardNoArr = explode("/",$todaysRecords->batch_card_no);            
            $numRecord = sprintf("%02d", $cardNoArr[0]+1);
        }
        $date = Carbon::today()->format('d/m/Y');
        $batchNo = $numRecord."/".$date;
        return $batchNo;
    }

    public function getBatchNumbers() {
        // return self::select('id','batch_card_no')->where('status', 1)->get();
        //$companyId = self::_getCompanyId();
        //dd($companyId);
        
         return self::with(['assignedProduct'])
                    ->select('id','batch_card_no','product_code')
                    ->whereStatus(1)
                    ->get();
    }

    public function getBatchDetails($id) {
        return self::with(['assignedProduct'])->find($id);
    }

    public function updatePlanAdded($id, $plan_added = 'yes') {      
        $collection = self::find($id);
        $collection->plan_added  = $plan_added;
        $collection->save();
        return $collection;
    }

   /* public function getPendingBatches() {
        return self::where('status', 1)->orderBy('id', 'DESC')->get();
        
        //'id','product_code','batch_card_no', 'batch_qty','status'
    }*/
}

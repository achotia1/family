<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\StoreSaleStockModel;

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

    public function hasStockProducts()
    {
        return $this->hasMany(StoreSaleStockModel::class, 'batch_id', 'id');
    }
    public function hasStockWastage()
    {
        return $this->hasMany(StoreWasteStockModel::class, 'batch_id', 'id');
    }
    public function getBatchCardNo($companyId) {
        
        $todaysRecords = self::where('status', 1)
                            ->where('company_id', $companyId)
                            ->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')
                            ->first();
        /*$todaysRecords = self::where('company_id', $companyId)
                            ->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')
                            ->first();*/
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

    public function getBatchNumbers($companyId,$opened=false) {       
         
        $modelQuery = self::with(['assignedProduct'])
                ->select('id','batch_card_no','product_code')
                ->where('company_id', $companyId)
                ->where('status', 1);
        if($opened){
            $modelQuery = $modelQuery
                        ->where('review_status', 'open'); 
        }
        $result = $modelQuery->get();
        return $result;   
        
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

    public function getClosedBatches($companyId) {      
        
        $modelQuery = self::select('id','batch_card_no')
                ->where('company_id', $companyId)
                ->where('review_status', 'closed')
                ->where('status', 1);        
        $result = $modelQuery->get();
        return $result;   
        
    }

    public function getWastageBatchNumbers($companyId) {  

         $modelQuery = self::with(['assignedProduct'])
                ->select('id','batch_card_no','product_code')
                ->where('company_id', $companyId)
                ->where('status', 1);
        /*if($opened){
            $modelQuery = $modelQuery
                        ->where('review_status', 'open'); 
        }*/
        $result = $modelQuery->get();
        return $result;   
        
    }

    public function updateClosedBatch($batchId, $yieldFlag = false, $outputFlag = false) {  
        //dd(config('constants.RCESTERCOMPANY'));
        $rcester_companyId = config('constants.RCESTERCOMPANY');
        $records = self::with([
            'hasProduction'=>function($q){
                $q->with(['hasProductionMaterials' => function($q){                    
                    $q->with('mateialName');
                    $q->with('hasLot');    
                }]);
                $q->with(['hasReturnMaterial' => function($q){
                    $q->with('hasReturnedMaterials');
                }]);
                $q->with(['hasOutMaterial']);
            }
        ])
        ->find($batchId);
        if($records->review_status == 'closed'){
            ## MAKE RETURNED ARRAY
            $returnedMateArr = array();
            if(isset($records->hasProduction->hasReturnMaterial->hasReturnedMaterials)){
                foreach($records->hasProduction->hasReturnMaterial->hasReturnedMaterials as $returnedMaterial){
                    $returnedMateArr[$returnedMaterial->lot_id] = $returnedMaterial->quantity;
                }
            }
            //dd($returnedMateArr);
            $yield = $cost_per_unit = $amountTotal = $finalTotal = 0;
            if($records->hasProduction){
                foreach($records->hasProduction->hasProductionMaterials as $material){
                    $returnedQty = 0;
                    if(isset($returnedMateArr[$material->lot_id]) && $returnedMateArr[$material->lot_id] > 0){
                        $returnedQty = $returnedMateArr[$material->lot_id];
                    }                    
                    /*$finalWeight = $material->quantity - $material->returned_quantity;*/
                    $finalWeight = $material->quantity - $returnedQty;
                    if($material->mateialName->material_type == 'Raw'){
                        $finalTotal = $finalTotal + $finalWeight;
                    }
                    $amount = ($finalWeight * $material->hasLot->price_per_unit);
                    $amountTotal = $amountTotal + $amount;
                }
                $matOutId = $records->hasProduction->hasOutMaterial->id;
                $courseQty = $records->hasProduction->hasOutMaterial->course_powder;
                $rejectionQty = $records->hasProduction->hasOutMaterial->rejection;
                $dustQty = $records->hasProduction->hasOutMaterial->dust_product;
                $looseQty = $records->hasProduction->hasOutMaterial->loose_material;
                $lossQty = $records->hasProduction->hasOutMaterial->loss_material;

                if($records->hasProduction->hasOutMaterial->sellable_qty > 0){
                    $sellableQty = $records->hasProduction->hasOutMaterial->sellable_qty;
                    $totalSellable = $sellableQty;
                    if($rcester_companyId == $records->company_id)
                        $totalSellable =  $records->hasProduction->hasOutMaterial->sellable_qty + $records->hasProduction->hasOutMaterial->loose_material + $records->hasProduction->hasOutMaterial->course_powder;
                    ## CALCULATE MANUFACTURING COST PER UNIT
                    $cost_per_unit = ($amountTotal)/$totalSellable;
                    ## CALCULATE YIELD
                    
                    if($finalTotal > 0)
                        $yield = ($totalSellable/$finalTotal) * 100;
                }            

            }
            ## UPDATE MANUFACTURING COST IN store_sales_stock        
            $collection = StoreSaleStockModel::where('batch_id', $batchId)->first();
            $preQty = $collection->quantity;
            $preBalQty = $collection->balance_quantity;
            $qtyDiff = $sellableQty - $collection->quantity;
            $collection->manufacturing_cost = $cost_per_unit;
            ## UPDATE QUANTITY AND BALANCE QUANTITY IN store_sales_stock 
            if($outputFlag){
                $collection->quantity = $sellableQty;
                $collection->balance_quantity = $preBalQty + $qtyDiff;
            }
            $collection->save();

            ## UPDATE YIELD IN store_out_materials
            if($yieldFlag && $matOutId > 0){
                $collOutMat = StoreOutMaterialModel::find($matOutId);
                $collOutMat->yield = $yield;
                $collOutMat->save();
            }
            ## UPDATE WASTAGE STOCK QUANTITY AND BALANCE QUANTITY IN store_waste_stock
            if($outputFlag){
                $collWastage = StoreWasteStockModel::where('batch_id', $batchId)->first();
                $preCourse = $collWastage->course;
                $preRejection = $collWastage->rejection;
                $preDust = $collWastage->dust;
                $preLoose = $collWastage->loose;

                $courseQty = $records->hasProduction->hasOutMaterial->course_powder;        
                $rejQty = $records->hasProduction->hasOutMaterial->rejection;
                $dustQty = $records->hasProduction->hasOutMaterial->dust_product;
                $looseQty = $records->hasProduction->hasOutMaterial->loose_material;

                $courseDiff = $courseQty - $preCourse;
                $rejDiff = $rejQty - $preRejection;
                $dustDiff = $dustQty - $preDust;
                $looseDiff = $looseQty - $preLoose;

                $collWastage->course = $courseQty;
                $collWastage->rejection = $rejQty;
                $collWastage->dust = $dustQty;
                $collWastage->loose = $looseQty;

                $collWastage->balance_course = $collWastage->balance_course + $courseDiff;
                $collWastage->balance_rejection = $collWastage->balance_rejection + $rejDiff;
                $collWastage->balance_dust = $collWastage->balance_dust + $dustDiff;
                $collWastage->balance_loose = $collWastage->balance_loose + $looseDiff;
                $collWastage->save();
            }
        }

        //return $collection;
        //dd($collWastage);        
    }
   /* public function getPendingBatches() {
        return self::where('status', 1)->orderBy('id', 'DESC')->get();
        
        //'id','product_code','batch_card_no', 'batch_qty','status'
    }*/
}

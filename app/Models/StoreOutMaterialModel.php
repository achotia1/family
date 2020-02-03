<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StoreOutMaterialModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_out_materials';

    protected $fillable = [
		'plan_id',
        'sellable_qty',
        'course_powder',        
        'rejection',
        'dust_product',
        'loose_material',
        'loss_material',
        'unfiltered',
        'rejection_water',
        'yield',
        'status'             
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function assignedPlan()
    {
        return $this->belongsTo(StoreProductionModel::class, 'plan_id', 'id');
    }
    public function hasStock()
    {       
        return $this->hasOne(StoreSaleStockModel::class, 'material_out_id', 'id');
    }

    public function updateMadeByMaterial($id, $companyId) {
        $outputDetails = self::with([
            'assignedPlan' => function($q)
            {  
                $q->with(['hasProductionMaterials' => function($q){
                    $q->with('mateialName');    
                }]);
                $q->with(['assignedBatch'=> function($q){
                    $q->with('assignedProduct');
                }]);
                $q->with(['hasReturnMaterial' => function($q){
                    $q->with('hasReturnedMaterials');
                }]);
                $q->with(['hasReuseWastage'=>function($q){
                    $q->with('hasReuseMaterials');
                }]);                
            }
        ])->where('company_id', $companyId)
        ->find($id);
        $wasteageWeight = $finalWeight = $yeild = $loss_material = $totalReusedWastage = 0;
        if($outputDetails){
            ## GET THE REUSED WASTAGE MATERIAL TOTAL            
            if(!empty($outputDetails->assignedPlan->hasReuseWastage->hasReuseMaterials)){
                foreach($outputDetails->assignedPlan->hasReuseWastage->hasReuseMaterials as $rKey=>$rVal){
                    $totalReusedWastage += $rVal->course + $rVal->rejection + $rVal->dust + $rVal->loose;
                }
            }

            $wasteageWeight = $outputDetails->sellable_qty + $outputDetails->course_powder + $outputDetails->rejection + $outputDetails->dust_product+$outputDetails->loose_material;
            if(isset($outputDetails->assignedPlan->hasProductionMaterials)){
                foreach($outputDetails->assignedPlan->hasProductionMaterials as $detail){
                    if($detail->mateialName->material_type == 'Raw'){
                        $returned = 0;
                        if(isset($outputDetails->assignedPlan->hasReturnMaterial->hasReturnedMaterials)){
                            foreach($outputDetails->assignedPlan->hasReturnMaterial->hasReturnedMaterials as $returnedMaterial){
                                if( $detail->lot_id == $returnedMaterial->lot_id)
                                    $returned = $returnedMaterial->quantity;                     
                            }
                        }                    
                        $finalWeight = $finalWeight + ($detail->quantity - $returned);
                    }
                }
            }
            // dd($finalWeight,$wasteageWeight);
            $totalForyield = $finalWeight + $totalReusedWastage;
            if($totalForyield > 0)
                $yield = ($outputDetails->sellable_qty/$totalForyield) * 100;

            if($finalWeight>0){                
                $loss_material = $finalWeight - $wasteageWeight;
                //$yield = ($outputDetails->sellable_qty/$finalWeight) * 100;
                
                $outputDetails->loss_material = $loss_material;
                $outputDetails->yield = $yield;
                $outputDetails->save();
            }
        }
        return $outputDetails;
    }
	
    public function rcUpdateMadeByMaterial($id, $companyId) {
        $outputDetails = self::with([
            'assignedPlan' => function($q)
            {  
                $q->with(['hasProductionMaterials' => function($q){
                    $q->with('mateialName');    
                }]);
                $q->with(['assignedBatch'=> function($q){
                    $q->with('assignedProduct');
                }]);
                $q->with(['hasReturnMaterial' => function($q){
                    $q->with('hasReturnedMaterials');
                }]);
                $q->with(['hasReuseWastage'=>function($q){
                    $q->with('hasReuseMaterials');
                }]);               
            }
        ])->where('company_id', $companyId)
        ->find($id);
        $wasteageWeight = $finalWeight = $yeild = $loss_material = $totalReusedWastage = 0;
        if($outputDetails){
            ## GET THE REUSED WASTAGE MATERIAL TOTAL            
            if(!empty($outputDetails->assignedPlan->hasReuseWastage->hasReuseMaterials)){
                foreach($outputDetails->assignedPlan->hasReuseWastage->hasReuseMaterials as $rKey=>$rVal){
                    $totalReusedWastage += $rVal->course + $rVal->rejection + $rVal->dust + $rVal->loose;
                }
            }
            $wasteageWeight = $outputDetails->sellable_qty + $outputDetails->loose_material + $outputDetails->course_powder + $outputDetails->rejection;
            ## FOR RC ONLY WATER OF REJECTION IS UNSELLABLE
            $totalSellable = $outputDetails->sellable_qty + $outputDetails->loose_material + $outputDetails->course_powder ;
            if(isset($outputDetails->assignedPlan->hasProductionMaterials)){
                foreach($outputDetails->assignedPlan->hasProductionMaterials as $detail){
                    if($detail->mateialName->material_type == 'Raw'){
                        $returned = 0;
                        if(isset($outputDetails->assignedPlan->hasReturnMaterial->hasReturnedMaterials)){
                            foreach($outputDetails->assignedPlan->hasReturnMaterial->hasReturnedMaterials as $returnedMaterial){
                                if( $detail->lot_id == $returnedMaterial->lot_id)
                                    $returned = $returnedMaterial->quantity;                     
                            }
                        }                    
                        $finalWeight = $finalWeight + ($detail->quantity - $returned);
                    }
                }
            }
            // dd($finalWeight,$wasteageWeight);
            $totalForyield = $finalWeight + $totalReusedWastage;
            if($totalForyield > 0)
                $yield = ($totalSellable/$totalForyield) * 100;

            if($finalWeight>0){                
                $loss_material = $finalWeight - $wasteageWeight;
                //$yield = ($totalSellable/$finalWeight) * 100;                
                $outputDetails->loss_material = $loss_material;
                $outputDetails->yield = $yield;
                $outputDetails->save();
            }
        }
        return $outputDetails;
    }
    public function getOutputRec($planId) {
        return self::select('id')->where('plan_id', $planId)->first();         
    }
    public function getOutputDetails($planId) {
        //return self::select('id')->where('plan_id', $planId)->first();
        return self::with([
                'assignedPlan'
                ])->where('plan_id', $planId)->first();
    }
	
    
}
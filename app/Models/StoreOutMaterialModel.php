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
        'loss_material',
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
            }
        ])->where('company_id', $companyId)
        ->find($id);
        $wasteageWeight = $finalWeight = $yeild = $loss_material = 0;
        if($outputDetails){
            $wasteageWeight = $outputDetails->sellable_qty + $outputDetails->course_powder + $outputDetails->rejection + $outputDetails->dust_product;
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
            //$wasteageWeight." >> ". $finalWeight;
            $loss_material = $finalWeight - $wasteageWeight;
            $yield = ($outputDetails->sellable_qty/$finalWeight) * 100;
            //$loss_material. " >> ".$yeild;
            $outputDetails->loss_material = $loss_material;
            $outputDetails->yield = $yield;
            $outputDetails->save();
        }
        return $outputDetails;
    }
	
    public function getOutputRec($planId) {
        return self::select('id')->where('plan_id', $planId)->first();         
    }
	
    
}
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
            }
        ])->where('company_id', $companyId)
        ->find($id);
        $wasteageWeight = $finalWeight = $yeild = $loss_material = 0;
        if($outputDetails){
            $wasteageWeight = $outputDetails->sellable_qty + $outputDetails->course_powder + $outputDetails->rejection + $outputDetails->dust_product;
            foreach($outputDetails->assignedPlan->hasProductionMaterials as $detail){
                if($detail->mateialName->material_type == 'Raw')
                    $finalWeight = $finalWeight + ($detail->quantity - $detail->returned_quantity);
            }
            //$wasteageWeight." >> ". $finalWeight;
            $loss_material = $finalWeight - $wasteageWeight;
            $yield = ($outputDetails->sellable_qty/$finalWeight) * 100;
            $loss_material. " >> ".$yeild;
            $outputDetails->loss_material = $loss_material;
            $outputDetails->yield = $yield;
            $outputDetails->save();
        }
        return $outputDetails;
    }
	
	
    
}
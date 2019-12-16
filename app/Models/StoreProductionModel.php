<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreProductionModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_productions';

    protected $fillable = [
		'company_id',
        'batch_id',
        'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function hasProductionMaterials()
    {
        return $this->hasMany(ProductionHasMaterialModel::class, 'production_id', 'id');
    }
    public function assignedBatch()
    {
        return $this->belongsTo(StoreBatchCardModel::class, 'batch_id', 'id');
    }
    public function hasOutMaterial()
    {       
        return $this->hasOne(StoreOutMaterialModel::class, 'plan_id', 'id');
    }

    public function hasReturnMaterial()
    {       
        return $this->hasOne(StoreReturnedMaterialModel::class, 'plan_id', 'id');
    }

    public function hasReuseWastage()
    {
        return $this->hasOne(StoreReuseWastageModel::class, 'plan_id', 'id');
    }

    public function getProductionPlans($companyId,$batchOpened=false) { 
        /*return self::with([
            'assignedBatch' => function($q){                
                $q->with('assignedProduct');
            }
        ])->where('company_id', $companyId)
        ->get();*/

        $modelQuery = self::with(
                        ['assignedBatch' => function($qu){
                            $qu->with('assignedProduct'); 
                        }]
                        )->where('company_id', $companyId);

        if(!empty($batchOpened) && $batchOpened==true){
            $modelQuery = $modelQuery->whereHas('assignedBatch', function($q){
                $q->where('review_status', '=', 'open');                
            });
        }

        return $modelQuery->get();
        
    } 
    
}

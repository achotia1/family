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
    public function getProductionPlans($companyId) {      
        return self::with([
            'assignedBatch' => function($q){
                $q->with('assignedProduct');
            }
        ])->where('company_id', $companyId)
        ->get();
        /*return self::with([
            'assignedBatch' => function($q){                
                $q->select(['id','batch_card_no']);
                $q->where('review_status','=', 'open');
                $q->with(['assignedProduct'=>function($p_query){
                        $p_query->select(['id','code', 'name']);
                    }
                ]);
            }
        ])->where('company_id', $companyId)
        ->get();*/
        
    } 
    
}

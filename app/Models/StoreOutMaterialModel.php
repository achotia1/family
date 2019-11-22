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
	
	
    
}
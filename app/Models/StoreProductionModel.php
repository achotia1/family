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
    
}

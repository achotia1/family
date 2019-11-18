<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionHasMaterialModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_production_has_materials';

    protected $fillable = [
		'production_id',
        'material_id',
        'lot_id',
        'quantity'
    ];
	public $timestamps = false;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

    public function associatedProduction()
    {
        return $this->belongsTo(StoreProductionModel::class, 'production_id', 'id');
    }
    public function mateialName()
    {
        return $this->belongsTo(StoreRawMaterialModel::class, 'material_id', 'id');
    }

    public function hasLot()
    {
        return $this->belongsTo(StoreInMaterialModel::class, 'lot_id', 'id');
    }
    
}

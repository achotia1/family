<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreReuseWastageHasMaterialsModel extends Model
{
    
    protected $table = 'store_reuse_wastage_has_materials';

    protected $fillable = [
		'reuse_wastage_id',
        'waste_stock_id',
        'course',
        'rejection',
        'dust',
        'loose',
    ];
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    public function assignedBatch()
    {
        return $this->belongsTo(StoreBatchCardModel::class, 'batch_id', 'id');
    }

    public function assignedWastageStock()
    {
        return $this->belongsTo(StoreWasteStockModel::class, 'waste_stock_id', 'id');
    }

    
}

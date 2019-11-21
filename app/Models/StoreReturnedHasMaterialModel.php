<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreReturnedHasMaterialModel extends Model
{
    
    protected $table = 'store_returned_materials_has_materials';

    protected $fillable = [
		'returned_id',
        'material_id',
        'lot_id',
        'quantity'
    ];
	//public $timestamps = false;
   //protected $dates = ['deleted_at'];
    public function material()
    {
        return $this->belongsTo(StoreRawMaterialModel::class, 'material_id', 'id');
    }

    public function lot()
    {
        return $this->belongsTo(StoreInMaterialModel::class, 'lot_id', 'id');
    }
    
    
}
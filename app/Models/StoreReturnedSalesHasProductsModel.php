<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreReturnedSalesHasProductsModel extends Model
{
    
    protected $table = 'store_returned_sales_has_products';

    protected $fillable = [
		'returned_id',
        'product_id',
        'batch_id',
        'quantity'
    ];
    /*public function material()
    {
        return $this->belongsTo(StoreRawMaterialModel::class, 'material_id', 'id');
    }

    public function lot()
    {
        return $this->belongsTo(StoreInMaterialModel::class, 'lot_id', 'id');
    }*/
    
    
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductsModel;
use App\Models\StoreBatchCardModel;

class StoreReturnedSalesHasProductsModel extends Model
{
    
    protected $table = 'store_returned_sales_has_products';

    protected $fillable = [
		'returned_id',
        'product_id',
        'batch_id',
        'quantity'
    ];

    public function assignedProduct()
    {
        return $this->belongsTo(ProductsModel::class, 'product_id', 'id');
    }

    public function assignedBatch()
    {
        return $this->belongsTo(StoreBatchCardModel::class, 'batch_id', 'id');
    }    
    
}
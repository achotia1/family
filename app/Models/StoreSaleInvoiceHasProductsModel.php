<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSaleInvoiceHasProductsModel extends Model
{
    
    protected $table = 'store_sale_invoice_has_products';

    protected $fillable = [
		'sale_invoice_id',
        'product_id',
        'batch_id',
        'quantity',
        'rate',
        'total_basic',
    ];
	public $timestamps = false;
   //protected $dates = ['deleted_at'];
    /*public function material()
    {
        return $this->belongsTo(StoreRawMaterialModel::class, 'material_id', 'id');
    }

    public function lot()
    {
        return $this->belongsTo(StoreInMaterialModel::class, 'lot_id', 'id');
    }*/
    
    
}
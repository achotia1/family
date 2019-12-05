<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ProductsModel;
use App\Models\StoreBatchCardModel;

class StoreSaleInvoiceHasProductsModel extends Model
{
    
    protected $table = 'store_sale_invoice_has_products';

    protected $fillable = [
        'id',
		'sale_invoice_id',
        'product_id',
        'batch_id',
        'quantity',
        'rate',
        'total_basic',
    ];
	public $timestamps = false;

    public function assignedProduct()
    {
        return $this->belongsTo(ProductsModel::class, 'product_id', 'id');
    }

    public function assignedBatch()
    {
        return $this->belongsTo(StoreBatchCardModel::class, 'batch_id', 'id');
    }
    public function assignedInvoice()
    {
        return $this->belongsTo(StoreSaleInvoiceModel::class, 'sale_invoice_id', 'id');
    }
    
    
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StoreReturnedSalesHasProductsModel;
use App\Models\StoreSaleInvoiceModel;
use App\Models\AdminUserModel;

class StoreReturnedSaleModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_returned_sales';

    protected $fillable = [
        'company_id',
        'user_id',
		'sale_invoice_id',
        'customer_id',
        'return_date',
        //'status'        
    ];

    protected $dates = ['deleted_at'];

    public function hasReturnedProducts()
    {
        return $this->hasMany(StoreReturnedSalesHasProductsModel::class, 'returned_id', 'id');
    }

    public function assignedSale()
    {
        return $this->belongsTo(StoreSaleInvoiceModel::class, 'sale_invoice_id', 'id');
    }

    public function assignedCustomer()
    {
        return $this->belongsTo(AdminUserModel::class, 'customer_id', 'id');
    }
  
}

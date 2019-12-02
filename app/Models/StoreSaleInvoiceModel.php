<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StoreSaleInvoiceHasProductsModel;
use App\Models\AdminUserModel;

class StoreSaleInvoiceModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_sale_invoice';

    protected $fillable = [
        'id',
        'company_id',
        'user_id',
        'customer_id',
		'invoice_no',
        'invoice_date',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function hasSaleInvoiceProducts(){
       return $this->hasMany(StoreSaleInvoiceHasProductsModel::class,'sale_invoice_id','id');
    }

    public function hasCustomer(){
        return $this->belongsTo(AdminUserModel::class, 'customer_id', 'id');
    }
    
    
}

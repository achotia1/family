<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreSaleInvoiceModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_sale_invoice';

    protected $fillable = [
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

    
    
}

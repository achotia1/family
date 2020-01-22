<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreTempProductsStockModel extends Model
{        
    protected $table = 'store_temp_product_stocks';

    protected $fillable = [
		'product_id',
        'name',
        'code',
        'opening_balance',        
        'received_qty',        
        'balance_qty',        
        'issued_qty',        
        'returned_qty'
    ];
}
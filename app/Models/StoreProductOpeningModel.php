<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class StoreProductOpeningModel extends Model
{        
    protected $table = 'store_product_openings';

    protected $fillable = [
		'product_id',
        'opening_bal',
        'opening_date'  
    ];
}
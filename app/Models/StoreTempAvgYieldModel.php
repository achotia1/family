<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreTempAvgYieldModel extends Model
{        
    protected $table = 'store_temp_avg_yields';

    protected $fillable = [
		'batch_id',
        'batch_card_no',
        'product_id',
        'product',        
        'out_id',
        'sellable_qty',
        'yield' 
    ];
}
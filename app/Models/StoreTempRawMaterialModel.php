<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreTempRawMaterialModel extends Model
{        
    protected $table = 'store_temp_raw_materials';

    protected $fillable = [
		'material_id',
        'name',
        'moq',        
        'unit',
        'material_type',
        'opening_balance',        
        'received_qty',        
        'balance_qty',        
        'issued_qty',        
        'returned_qty',
        'status'        
    ];
}
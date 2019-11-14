<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreRawMaterialModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_raw_materials';

    protected $fillable = [
		'name',
        'moq',
        'unit',        
        'balance_stock',
        'material_type',
        'status'        
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getMaterialNumbers() {
        return StoreRawMaterialModel::select('id','name')->where('status', 1)->get();
    }
}
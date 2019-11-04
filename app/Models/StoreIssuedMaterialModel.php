<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreIssuedMaterialModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_issued_materials';

    protected $fillable = [
		'material_id',
        'issue_date',
        'quantity',
        'bill_number',
        'status'        
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /*public function getMaterialNumbers() {
        return StoreRawMaterialModel::select('id','name')->get();
    }*/
}

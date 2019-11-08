<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreProductionModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_productions';

    protected $fillable = [
		'batch_no',
        'material_id',
        'quantity',
        'unit',
        'status'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function associatedMateials()
    {
        return $this->belongsTo(StoreRawMaterialModel::class, 'material_id', 'id');
    }
    
}

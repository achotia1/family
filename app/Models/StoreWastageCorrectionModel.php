<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreWastageCorrectionModel extends Model
{
        
    protected $table = 'store_wastage_corrections';
	
    protected $fillable = [
        'user_id',
		'wastage_id',
        'previous_cbalance',
        'corrected_cbalance',
        'previous_rbalance',
        'corrected_rbalance',
        'previous_dbalance',
        'corrected_dbalance',
        'previous_lbalance',
        'corrected_lbalance' 
    ];
    
	public $timestamps = false;
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    
    /*public function hasLots()
    {
        return $this->belongsTo(StoreInMaterialModel::class, 'lot_id', 'id');
    }*/   
}
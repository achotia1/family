<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StoreLotCorrectionModel extends Model
{
        
    protected $table = 'store_lot_corrections';
	
    protected $fillable = [
        'user_id',
		'lot_id',
        'previous_balance',
        'corrected_balance' 
    ];
    
	public $timestamps = false;
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    
    public function hasLots()
    {
        return $this->belongsTo(StoreInMaterialModel::class, 'lot_id', 'id');
    }    
}
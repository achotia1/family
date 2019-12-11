<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use Carbon\Carbon;

class StoreStockCorrectionModel extends Model
{
        
    protected $table = 'store_stock_corrections';
	
    protected $fillable = [
        'user_id',
		'stock_id',
        'previous_balance',
        'corrected_balance' 
    ];
    
	public $timestamps = false;
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    
    public function hasStocks()
    {
        return $this->belongsTo(StoreSaleStockModel::class, 'stock_id', 'id');
    }    
}
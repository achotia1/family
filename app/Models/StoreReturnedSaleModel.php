<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreReturnedSaleModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_returned_sales';

    protected $fillable = [
        'company_id',
        'user_id',
		'sale_invoice_id',
        'customer_id',
        'return_date',
        //'status'        
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /*public function getBatchReturnMaterial($batchNo) {
        $arrReturnData = array();
        $returnedData =  self::select('material_id','quantity')->where('status', 1)->where('batch_no', $batchNo)->get();
        foreach($returnedData as $key => $data){            
            $arrReturnData[$data->material_id] = $data->quantity;
        }
        return  $arrReturnData;
    }
    public function assignedProductionPlan()
    {
        return $this->belongsTo(StoreProductionModel::class, 'plan_id', 'id');
    }

    public function hasReturnedMaterials()
    {
        return $this->hasMany(StoreReturnedHasMaterialModel::class, 'returned_id', 'id');
    }

    public function hasBatch()
    {
        return $this->belongsTo(StoreBatchCardModel::class, 'batch_id', 'id');
    }*/
}

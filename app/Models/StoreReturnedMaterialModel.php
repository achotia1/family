<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreReturnedMaterialModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_returned_materials';

    protected $fillable = [
		'batch_no',
        'material_id',
        'return_date',
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

    public function getBatchReturnMaterial($batchNo) {
        $arrReturnData = array();
        $returnedData =  self::select('material_id','quantity')->where('status', 1)->where('batch_no', $batchNo)->get();
        foreach($returnedData as $key => $data){            
            $arrReturnData[$data->material_id] = $data->quantity;
        }
        return  $arrReturnData;
    }
}

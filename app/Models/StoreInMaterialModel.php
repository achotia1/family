<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StoreInMaterialModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_in_materials';

    protected $fillable = [
		'material_id',
        'lot_no',
        'lot_qty',        
        'price_per_unit',
        'lot_balance',
        'status'             
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function hasMateials()
    {
        return $this->belongsTo(StoreRawMaterialModel::class, 'material_id', 'id');
    }

    /*public function getMaterialNumbers() {
        return StoreRawMaterialModel::select('id','name')->where('status', 1)->get();
    }*/

    public function geLotNo() {
        $todaysRecords = self::whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->first();
        $lotNoArr[0] = 0;
        $numRecord = sprintf("%02d", 1);
        if(!empty($todaysRecords)){
            $lotNoArr = explode("/",$todaysRecords->lot_no);
            $no = substr($lotNoArr[0], 2);
            $numRecord = sprintf("%02d", $no+1);
        }
        $date = Carbon::today()->format('d/m/Y');
        $lotNo = "R-".$numRecord."/".$date;
        return $lotNo;
    }
    public function getBalanceLots($material_id, $companyId=0) {
        $balanceMaterials = array();        
        $modelQuery = self::select('id','lot_no')
        ->where('material_id',$material_id)
        ->where('status', 1);
        if($companyId > 0){
            $modelQuery = $modelQuery->where('company_id', $companyId);
        }
        $balanceMaterials = $modelQuery->get()->toArray();
        /*$balanceMaterials = self::select('id','lot_no')->where('material_id',$material_id)->where('status', 1)->get()->toArray();*/
        //dd($balanceMaterials);

        return $balanceMaterials;
    }
    public function updateBalance($inMaterialcollection, $quantity, $preQty=0) {
        $return = false;        
        //$inLotBal = $inMaterialcollection->lot_balance - $quantity;        
        $inLotBal = ($inMaterialcollection->lot_balance) - $quantity;        
        $inMaterialcollection->lot_balance = $inLotBal;
        //dd($inMaterialcollection);
        if($inMaterialcollection->save())
            $return = true;
        return $return;
    }
}
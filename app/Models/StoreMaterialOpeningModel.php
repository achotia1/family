<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class StoreMaterialOpeningModel extends Model
{
        
    protected $table = 'store_material_openings';

    protected $fillable = [
		'material_id',
        'opening_bal',
        'opening_date'  
    ];
    
   /* public function rawMaterial()
    {
        return $this->belongsTo(StoreRawMaterialModel::class, 'material_id', 'id');
    }*/
        
    public function updateOpeningBals($prevArr, $currArr=array()) {
        ## REMOVE PREVIOUS QUANTITES FROM store_material_openings
        $openingDate =  Carbon::today()->format('Y-m-d');
        if(!empty($prevArr)){
	        foreach($prevArr as $prevKay=>$prevVal){
		        $removeQty = 0;	        
		        $rawopncollection = self::where('material_id', $prevKay)->where('opening_date',$openingDate)->first();            
		        if(!empty($rawopncollection)){                
		            foreach($prevVal as $prevLot=>$prevQty){
		                $removeQty = $removeQty + $prevQty;
		            }	            
		            $rawopncollection->opening_bal = $rawopncollection->opening_bal + $removeQty;
		            $rawopncollection->save();
		        }            
	        }
		}
        ## ADD CURRENT QUANTITES IN store_material_openings
        if(!empty($currArr)){
	        foreach($currArr as $currKay=>$currVal){
	            $addQty = 0;            
	            $matOpnCollection = self::where('material_id', $currKay)->where('opening_date',$openingDate)->first();            
	            if(!empty($matOpnCollection)){                
	                foreach($currVal as $currLot=>$currQty){
	                    $addQty = $addQty + $currQty;
	                }                
	                $matOpnCollection->opening_bal = $matOpnCollection->opening_bal - $addQty;
	                $matOpnCollection->save();
	            }    
	        }
	    }

    }
    public function updateOpeningBalsNew($cDate, $prevArr, $currArr=array(), $flag=false) {
        ## REMOVE PREVIOUS QUANTITES FROM store_material_openings
        
        if(!empty($prevArr)){
	        foreach($prevArr as $prevKay=>$prevVal){
	            $removeQty = 0;
	            foreach($prevVal as $prevLot=>$prevQty){
	                $removeQty = $removeQty + $prevQty;
	            }	            
	            if($flag){
					$rawopncollection = self::where('material_id', $prevKay)->where('opening_date', '>=',$cDate)->get();
				} else {
					$rawopncollection = self::where('material_id', $prevKay)->where('opening_date', '>',$cDate)->get();	
				}
	                        
	            if(!empty($rawopncollection)){
	                foreach ($rawopncollection as $item) {
	                    //$objMOpen = new StoreMaterialOpeningModel;
	                    $openingColl = self::find($item['id']);
	                    $openingColl->opening_bal += $removeQty;                    
	                    $openingColl->save();
	                }                
	            }            
        	}
		}
        ## ADD CURRENT QUANTITES IN store_material_openings
        if(!empty($currArr)){
	        foreach($currArr as $currKay=>$currVal){
	            $addQty = 0;
	            foreach($currVal as $currLot=>$currQty){
	                $addQty = $addQty + $currQty;
	            }
	            
	            if($flag){
	            	$matOpnCollection = self::where('material_id', $currKay)->where('opening_date', '>=',$cDate)->get();
				} else {
					$matOpnCollection = self::where('material_id', $currKay)->where('opening_date', '>',$cDate)->get();	
				}
	            	                      
	            if(!empty($matOpnCollection)){                
	                
	                foreach ($matOpnCollection as $citem) {	                    
	                    $openingCColl = self::find($citem['id']);
	                    $openingCColl->opening_bal -= $addQty;
	                    $openingCColl->save();
	                }	                
	            }   
	        }
	    }
    }
}
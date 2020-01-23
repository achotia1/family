<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class StoreProductOpeningModel extends Model
{        
    protected $table = 'store_product_openings';

    protected $fillable = [
		'product_id',
        'opening_bal',
        'opening_date'  
    ];
    public function testMe() {
    	return "test";
    }
    public function updateStockOpeningBals($cDate, $prevArr, $currArr=array(), $flag=false) {
        ## REMOVE PREVIOUS QUANTITES FROM store_material_openings
        
        if(!empty($prevArr)){
	        foreach($prevArr as $prevKay=>$prevVal){
	            $removeQty = 0;
	            foreach($prevVal as $prevBatch=>$prevQty){
	                $removeQty = $removeQty + $prevQty;
	            }	            
	            if($flag){
					$rawopncollection = self::where('product_id', $prevKay)->where('opening_date', '>=',$cDate)->get();
				} else {
					$rawopncollection = self::where('product_id', $prevKay)->where('opening_date', '>',$cDate)->get();	
				}
	                        
	            if(!empty($rawopncollection)){
	                foreach ($rawopncollection as $item) {	                    
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
	            	$matOpnCollection = self::where('product_id', $currKay)->where('opening_date', '>=',$cDate)->get();
				} else {
					$matOpnCollection = self::where('product_id', $currKay)->where('opening_date', '>',$cDate)->get();	
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
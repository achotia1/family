<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



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
        
    /*public function getOpeningBals($companyId) {
        return self::with(['assignedProduct'])
                    ->where('company_id',$companyId)
                    ->groupBy('product_id')
                    ->get();

    }*/

}
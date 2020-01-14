<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreRawMaterialModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_raw_materials';

    protected $fillable = [
		'name',
        'moq',
        'unit',        
        'balance_stock',
        'material_type',
        'status'        
    ];

    public function hasInMaterials()
    {
        return $this->hasMany(StoreInMaterialModel::class, 'material_id', 'id');
    }

    public function hasOpeningMaterials()
    {
        return $this->hasMany(StoreMaterialOpeningModel::class, 'material_id', 'id');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /*public function getLotMaterials($companyId=0) {
        $balanceMaterials = array();
        $lotmaterialIds = self::where('status', 1)->with(['hasInMaterials'=> function($q){
                $q->where('status', 1);
                $q->where('lot_qty', '>', 0);                 
            }])
        ->get(['id','name'])->toArray();
        foreach($lotmaterialIds as $mval){
            if(!empty($mval['has_in_materials'])){
                $balanceMaterials[$mval['id']]['id'] = $mval['id']; //$mval['name'];
                $balanceMaterials[$mval['id']]['name'] = $mval['name'];   
            }                      
        }        
        return $balanceMaterials;
    }*/
    public function getLotMaterials($companyId=0) {
        $balanceMaterials = array();
        $modelQuery = self::where('status', 1);
        if($companyId > 0){
            $modelQuery = $modelQuery->where('company_id', $companyId);
            $modelQuery = $modelQuery->with(['hasInMaterials'=> function($q) use($companyId){
                /*$q->where('status', 1);*/
                $q->where('lot_balance', '>', 0);
                $q->where('company_id', $companyId);                 
            }]);  
        } else {            
            $modelQuery = $modelQuery->with(['hasInMaterials'=> function($q){
                /*$q->where('status', 1);*/
                $q->where('lot_qty', '>', 0);                
            }]);  
        }
        
        $lotmaterialIds = $modelQuery->orderBy('store_raw_materials.name', 'ASC')->get(['id','name'])->toArray();        
        foreach($lotmaterialIds as $mval){
            if(!empty($mval['has_in_materials'])){
                $balanceMaterials[$mval['id']]['id'] = $mval['id']; //$mval['name'];
                $balanceMaterials[$mval['id']]['name'] = $mval['name'];   
            }                      
        }        
        return $balanceMaterials;
    }

    public function getMaterialNumbers($company_id=false) {
        // dd($company_id);
        return StoreRawMaterialModel::select('id','name')
                ->where('status', 1)
                ->where('company_id', $company_id)
                ->orderBy('name', 'ASC')
                ->get();
    }

}
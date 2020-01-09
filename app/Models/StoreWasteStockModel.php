<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StoreWasteStockModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_waste_stock';

    protected $fillable = [
		'material_out_id',
        'product_id',
        'batch_id',        
        'course',
        'balance_course',
        'rejection',                    
        'balance_rejection',                    
        'dust',                    
        'balance_dust',                    
        'loose',                    
        'balance_loose',                    
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function outMaterial()
    {
        return $this->belongsTo(StoreOutMaterialModel::class, 'material_out_id', 'id');
    }
    public function assignedBatch()
    {
        return $this->belongsTo(StoreBatchCardModel::class, 'batch_id', 'id');
    }
    public function assignedProduct()
    {
        return $this->belongsTo(ProductsModel::class, 'product_id', 'id');
    }
    public function addWasteStock($data) {
        $stockWasteReturn = false;
        if(!empty($data)){
            $details = self::where('material_out_id', $data['material_out_id'])->first();
            if(!$details){
                $stockWasteReturn = self::insert($data);              
            }
        }
        return $stockWasteReturn;
    }
    public function getWastageStockProducts($companyId) {
        return self::with(['assignedProduct'])
                    ->where('company_id',$companyId)
                    ->groupBy('product_id')
                    ->get();

    }

}
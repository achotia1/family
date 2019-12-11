<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StoreSaleStockModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_sales_stock';

    protected $fillable = [
		'material_out_id',
        'product_id',
        'batch_id',        
        'manufacturing_cost',
        'quantity',
        'balance_quantity'                     
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
    public function addSalesStock($data) {
        $stockReturn = false;
        if(!empty($data)){
            $details = self::where('material_out_id', $data['material_out_id'])->first();
            if(!$details){
                $stockReturn = self::insert($data);              
            }
        }
        return $stockReturn;
    }
    public function getProductStock($productId, $companyId) {
        $stockData = self::select('id', 'batch_id', 'balance_quantity')
                    ->with(['assignedBatch' => function($q){
                        //$q->select('batch_card_no');
                    }])
                    ->where('product_id', $productId)
                    ->where('balance_quantity', '>', 0)
                    ->where('company_id', $companyId)
                    ->orderBy('balance_quantity','DESC')
                    ->get();
        return $stockData;
    }

}
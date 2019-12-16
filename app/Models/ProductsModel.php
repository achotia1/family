<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserHasProductsModel;

class ProductsModel extends Model
{
    protected $table = 'products';

    protected $fillable = [
		'name',
        'code',
        'status'
    ];

    public function assignedUserProducts()
    {
    	return $this->hasMany(UserHasProductsModel::class, 'product_id', 'id');
    }
    
    public function getProducts($companyId=false) {
        
        return ProductsModel::select('id','name','code')
                            ->where('status','1')
                            ->where('company_id',$companyId)
                            ->orderBy('code', 'ASC')
                            ->get();
    }

    public function getDeviatedProducts($companyId=false) {        
        $modelQuery = ProductsModel::select('product_id','name','code')
                    ->leftjoin('store_sales_stock','store_sales_stock.product_id', '=', 'products.id');
        if($companyId){
           $modelQuery = $modelQuery
                        ->where('store_sales_stock.company_id', $companyId);
        }        
                    
        $result = $modelQuery
                    ->whereNotNull('store_sales_stock.balance_corrected_at')
                    ->groupBy('products.id')
                    ->get();
        return $result;
    }
}

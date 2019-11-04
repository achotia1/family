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
    
    public function getProducts() {
        return ProductsModel::select('id','name')->where('status','1')->get();
    }
}

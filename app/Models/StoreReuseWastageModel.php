<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreReuseWastageModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_reuse_wastage';

    protected $fillable = [
		'company_id',
        'user_id',
        'plan_id',
        'batch_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    public function hasReuseMaterials()
    {
        return $this->hasMany(StoreReuseWastageHasMaterialsModel::class, 'reuse_wastage_id', 'id');
    }


    
}

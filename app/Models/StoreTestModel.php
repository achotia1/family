<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreTestModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'store_test';

    protected $fillable = [
		'name',
        'email'
    ];    

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class StoreWastageOpeningModel extends Model
{        
    protected $table = 'store_wastage_openings';

    protected $fillable = [
		'company_id',
        'balance_course',
        'balance_rejection',
        'balance_dust',
        'balance_loose',
        'opening_date' 
    ];   
}
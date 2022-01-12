<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CitiesModel extends Model
{
    protected $table 		= 'cities'; 

    protected $fillable = [
        'name', 'state_id', 'created_at', 'updated_at'
    ];

}

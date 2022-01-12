<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StatesModel extends Model
{
    protected $table 		= 'states'; 

    protected $fillable = [
        'name', 'created_at', 'updated_at'
    ];

}

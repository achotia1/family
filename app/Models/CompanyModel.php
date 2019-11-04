<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';

    protected $fillable = [
		'name',
		'logo'
    ];
}

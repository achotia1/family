<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Permission\Traits\HasRoles;

class AdminUserModel extends Authenticatable
{
    use Notifiable,HasRoles;

    protected $table 		= 'users'; 

    protected $guard_name 	= 'admin';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}

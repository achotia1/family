<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FamilyHeadsModel extends Model
{
    protected $table 		= 'family_heads'; 

    protected $fillable = [
        'first_name', 'last_name', 'birth_date', 'mobile_no', 'address', 'state_id', 'city_id', 'pincode', 'martial_status', 'hobbies', 'photo', 'education', 'member_type', 'created_at', 'updated_at'
    ];

    public function hasFamilyMembers()
    {
        return $this->hasMany(FamilyHeadHasMemberModel::class, 'family_head_id', 'id');
    }

}

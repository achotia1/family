<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyHeadHasMemberModel extends Model
{
    
    protected $table = 'family_head_has_members';

    protected $fillable = [
		'family_head_id',
        'member_id',
        'created_at',
        'updated_at'
    ];

    public function associatedFamilyMembers()
    {
        return $this->belongsTo(FamilyHeadsModel::class, 'member_id', 'id');
    }
    
    
}
<?php

namespace App\Models;

use App\Models\StakeholderPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StakeholderRole extends Model
{
    protected $table = 'stakeholder_roles';

    protected $fillable = ['name', 'slug', 'description','status'];

    public function permissions()
    {
        return $this->belongsToMany(
            StakeholderPermission::class,
            'stakeholder_rps',
            'stakeholder_role_id',
            'stakeholder_permission_id'
        );
    }

    public function stakeholders()
    {
        return $this->belongsToMany(
            Stakeholder::class,
            'stakeholder_has_roles'
        );
    }

    public function scopeIsNec()
    {
        return $this->whereIn('role_id', [1,2,3,4,6,7]);
    }
}

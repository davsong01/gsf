<?php

namespace App\Models;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\StakeholderRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Stakeholder extends Authenticatable
{
    use Notifiable;

    protected $guard = 'stakeholder';

    protected $fillable = [
        'signature',
        'name',
        'phone',
        'email',
        'field_id',
        'zone_id',
        'chapter_id',
        'role_id',
        'password',
        'day',
        'month',
        'year',
        'portfolio',
        'status'
    ];
    
    protected $hidden = [
        'password',
    ];

    public function zone(){
        return $this->belongsTo(Zone::class);
    }

    public function field(){
        return $this->belongsTo(Field::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email','email');
    }

    // public function roles()
    // {
    //     return $this->belongsToMany(
    //         StakeholderRole::class,
    //         'stakeholder_has_roles'
    //     );
    // }
    public function role()
    {
        return $this->belongsTo(StakeholderRole::class, 'role_id')->withDefault();
    }

    public function permissions()
    {
        return $this->role()
            ->with('permissions:id,slug')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id')
            ->values();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()
            ->contains('slug', $permission);
    }
}

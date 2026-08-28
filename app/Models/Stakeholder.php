<?php

namespace App\Models;

use App\Models\Otp;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\StakeholderRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Stakeholder extends Authenticatable
{
    use Notifiable, SoftDeletes;

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
        'designation_id',
        'password',
        'day',
        'month',
        'year',
        'status',
        'credentials_sent',
        'access_appraisal_system',
        'access_appraisal_evaluation',
        'last_login'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'role_id' => 'integer',
        'zone_id' => 'integer',
        'field_id' => 'integer',
        'chapter_id' => 'integer',
        'designation_id' => 'integer',
        'credentials_sent' => 'boolean',
        'access_appraisal_system' => 'boolean',
        'access_appraisal_evaluation' => 'boolean',
        'deleted_at' => 'datetime',
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

    public function designation()
    {
        return $this->belongsTo(StakeholderDesignation::class, 'designation_id');
    }

    public function appraisal()
    {
        return $this->hasOne(StakeholderAppraisal::class, 'appraisee_id');
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

    public function otps()
    {
        return $this->morphMany(Otp::class, 'userable');
    }

}

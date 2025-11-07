<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinistryField extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'field_usage',
        'name',
        'label',
        'type',
        'options',
        'registration_types',
        'required',
        'status',
        'has_other_option',
        'onchange',
        'depends_on',
        'display_order',
    ];

    protected $casts = [
        'options' => 'array',
        'registration_types' => 'array',
        'depends_on' => 'array',
        'required' => 'boolean',
        'status' => 'boolean',
        'has_other_option' => 'boolean',
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }
}

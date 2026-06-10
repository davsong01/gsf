<?php

namespace App\Models;


use App\Models\Field;
use App\Models\Chapter;
use App\Models\ReportRejection;
use App\Models\StakeholderPayment;
use Illuminate\Database\Eloquent\Model;

class StakeholderReport extends Model
{
    protected $guarded = [];
    protected $casts = [
        'zone_status' => 'integer',
        'field_status' => 'integer',
        'national_status' => 'integer',
        'edit_mode' => 'integer',
    ];
    
    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

    public function field(){
        return $this->belongsTo(Field::class);
    }

    public function stakeholderpayment(){
        return $this->hasOne(StakeholderPayment::class, 'report_id');
    }

    public function zone(){
        return $this->belongsTo(Zone::class);
    }

    public function stakeholder()
    {
        return $this->belongsTo(Stakeholder::class);
    }

    public function answers()
    {
        return $this->hasMany(StakeholderReportAnswer::class, 'report_id');
    }

}

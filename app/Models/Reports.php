<?php

namespace app\Models;


use App\Models\Field;
use App\Models\Chapter;
use App\Models\ReportRejection;
use App\Models\StakeholderPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reports extends Model
{
    use SoftDeletes;

    protected $guarded = [];
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
    public function rejections(){
        return $this->hasMany(ReportRejection::class, 'report_id');
    }

}

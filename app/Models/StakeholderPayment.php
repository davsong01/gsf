<?php

namespace App\Models;

use App\Models\Chapter;
use App\Models\Reports;
use Illuminate\Database\Eloquent\Model;


class StakeholderPayment extends Model
{
    protected $guarded = [];
    
    public function report(){
        return $this->belongsTo( StakeholderReport::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

}

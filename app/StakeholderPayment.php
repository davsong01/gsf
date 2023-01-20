<?php

namespace App;

use App\Chapter;
use App\Reports;
use Illuminate\Database\Eloquent\Model;


class StakeholderPayment extends Model
{
    protected $guarded = [];
    
    public function report(){
        return $this->belongsTo(Reports::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

}

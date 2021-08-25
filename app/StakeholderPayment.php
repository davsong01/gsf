<?php

namespace App;

use App\Reports;
use Illuminate\Database\Eloquent\Model;


class StakeholderPayment extends Model
{
    protected $guarded = [];
    
    public function report(){
        return $this->belongsTo(Reports::class);
    }

}

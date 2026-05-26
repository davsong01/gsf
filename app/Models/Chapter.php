<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Field;
use App\Models\Payment;
use App\Models\Stakeholder;
use App\Models\StakeholderDesignation;
use App\Models\StakeholderReport;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $guarded = [];

    public function users(){
        return $this->hasMany(User::class, 'chapter_id');
    }

    public function members()
    {
        return User::where('chapter_id', $this->id)->where('is_graduated', 0)->where('role', '<>', 1)->latest()->get();
    }

    public function alumni()
    {
        return User::where('chapter_id', $this->id)->where('is_graduated', 1)->where('role', '<>', 1)->latest()->get();
    }

    public function stakeholder(){
        return $this->hasOne(Stakeholder::class);
    }

    public function reports(){
        return $this->hasMany(StakeholderReport::class);
    }

    public function stakeholders()
    {
        return $this->hasMany(Stakeholder::class);
    }

    public function zone(){
        return $this->belongsTo(Zone::class);
    }

    public function field(){
        return $this->belongsTo(Field::class);
    }

    public function events(){
        return $this->hasMany(Event::class);
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class);
    }

    public function registerdParticipants()
    {
        return $this->hasManyThrough(Transaction::class, User::class);
    }

    public function chapterPresident()
    {
        return $this->hasOne(User::class)->where('designation_id', 143);
    }

    public function relatedByZone()
    {
        return Chapter::where('zone_id', $this->zone_id)
                    ->where('id', '!=', $this->id);
    }

    public function relatedByField()
    {
        return Chapter::where('field_id', $this->field_id)
                    ->where('id', '!=', $this->id);
    }

    public function reportCompliance()
    {
        // 1. Establish boundaries from January 2026 to the current time context
        $startDate = Carbon::parse('2026-01-01')->startOfMonth();
        $currentDate = Carbon::now()->startOfMonth();

        if ($currentDate->isBefore($startDate)) {
            return 100;
        }

        // 3. Count total expected operational reporting months (inclusive of current month)
        $expectedMonthsCount = $startDate->diffInMonths($currentDate) + 1;

        // 4. Count unique reports submitted by this chapter from Jan 2026 onwards
        // Assumes your StakeholderReport records rely on a standard 'created_at' timestamp
        // $submittedReportsCount = $this->reports()
        //     ->where('created_at', '>=', $startDate)
        //     ->count();

        // Alternative if your system maps submissions strictly by legacy integer columns 'year' & 'month':
        $submittedReportsCount = $this->reports()
            ->where(function ($query) {
                $query->where('year', '>', 2026)
                      ->orWhere(function ($q) {
                          $q->where('year', 2026)
                            ->where('month', '>=', 1);
                      });
            })->count();

        if ($expectedMonthsCount === 0) {
            return 100;
        }

        $percentage = ($submittedReportsCount / $expectedMonthsCount) * 100;

        return round(min($percentage, 100), 1);
    }
}

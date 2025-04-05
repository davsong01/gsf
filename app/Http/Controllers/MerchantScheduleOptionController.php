<?php

namespace App\Http\Controllers;

use App\Services\MerchantScheduleOptionService;
use Illuminate\Http\Request;

class MerchantScheduleOptionController extends Controller{
    protected $scheduleService;
    protected $httpResponseService;

    public function __construct(MerchantScheduleOptionService $scheduleService, HttpResponseService $httpResponseService)
    {
        $this->scheduleService = $scheduleService;
        $this->httpResponseService = $httpResponseService;
    }

    public function generate($merchantId)
    {
        return response()->json($this->scheduleService->generateWeeklySchedule($merchantId));
    }

    public function store(Request $request, $merchantId)
    {
        $validated = $request->validate([
            'selected_slots' => 'required|array'
        ]);

        $this->scheduleService->storeSchedule($merchantId, $validated['selected_slots']);

        return response()->json(['message' => 'Schedule saved successfully']);
    }

    public function getMerchantScheduleOption($merchantId)
    {
        $schedule = $this->scheduleService->getMerchantScheduleOption($merchantId);
        dd($schedule);
        if (!$schedule) {
            return $this->httpResponseService->error('Schedule not found', 404);
        }

        return $this->httpResponseService->success([
            'merchant_id' => $merchantId,
            'weekly_schedule' => $schedule
        ]);
    }

    private function reorderSchedule($weeklySchedule, $today)
    {
        $daysOfWeek = [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday"
        ];

        $startIndex = array_search($today, $daysOfWeek);
        $orderedDays = array_merge(
            array_slice($daysOfWeek, $startIndex),
            array_slice($daysOfWeek, 0, $startIndex)
        );

        $reorderedSchedule = [];

        foreach ($orderedDays as $day) {
            if (isset($weeklySchedule[$day])) {
                $reorderedSchedule[] = [
                    'day' => $day,
                    'time' => $weeklySchedule[$day]
                ];
            }
        }

        return $reorderedSchedule;
    }
}
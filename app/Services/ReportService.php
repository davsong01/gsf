<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Payment;
use App\Models\ConferenceEdition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportService
{
    public function sessionRange(): array
    {
        $sessions = [];
        $currentYear = (int) date('Y');

        for ($year = 2023; $year <= $currentYear; $year++) {
            $session = $year . '/' . ($year + 1);
            $sessions[] = [
                'label' => $session,
                'value' => $session,
            ];
        }

        return $sessions;
    }
}

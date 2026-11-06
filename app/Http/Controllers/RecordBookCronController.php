<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RecordBookCronController extends Controller
{
    public function processApproved(Request $request, string $token)
    {
        $configuredToken = (string) config('services.record_book_cron_token', '');
        abort_unless($configuredToken !== '' && hash_equals($configuredToken, $token), 403);

        @set_time_limit(0);
        $exitCode = Artisan::call('record-books:process-approved');
        $output = trim(Artisan::output());

        return response()->json([
            'status' => $exitCode === 0 ? 'ok' : 'completed_with_errors',
            'output' => $output ?: 'No approved record-book images were waiting for extraction.',
        ], $exitCode === 0 ? 200 : 207);
    }
}

<?php

namespace App\Errors\Controllers;

use App\Errors\Services\SystemLogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function __construct(protected SystemLogService $logService)
    {
    }

    public function index()
    {
        $logs = $this->logService->getRecentLogs(50);
        $recurring = $this->logService->getRecurringErrors(20);

        return view('admin.errors.system_logs.index', compact('logs', 'recurring'));
    }

    public function recentLogs(Request $request)
    {
        return response()->json($this->logService->getRecentLogs((int) $request->input('limit', 100)));
    }

    public function recurringErrors(Request $request)
    {
        return response()->json($this->logService->getRecurringErrors((int) $request->input('limit', 10)));
    }

    public function clearDatabaseLogs()
    {
        $this->logService->clearDatabaseLogs();

        return back()->with('message', 'All database logs cleared.');
    }

    public function delete(int $id)
    {
        $this->logService->deleteError($id);

        return back()->with('message', 'Log deleted successfully.');
    }
}

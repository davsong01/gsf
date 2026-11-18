<?php

namespace App\Errors\Controllers;

use App\Errors\Services\SystemLogService;
use App\Errors\Models\SystemLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function __construct(protected SystemLogService $logService)
    {
    }

    public function index(Request $request)
    {
        $level = strtoupper((string) $request->query('level', ''));
        $search = trim((string) $request->query('q', ''));
        $allowedLevels = ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'];

        $logsQuery = SystemLog::query()->latest('logged_at');
        if (in_array($level, $allowedLevels, true)) {
            $logsQuery->where('level', $level);
        } else {
            $level = '';
        }
        if ($search !== '') {
            $logsQuery->where(function ($query) use ($search) {
                $query->where('message', 'like', '%' . $search . '%')
                    ->orWhere('source', 'like', '%' . $search . '%');
            });
        }

        $logs = $logsQuery->paginate(25)->withQueryString();
        $recurring = $this->logService->getRecurringErrors(20);
        $stats = [
            'total' => SystemLog::count(),
            'critical' => SystemLog::whereIn('level', ['CRITICAL', 'ALERT', 'EMERGENCY'])->count(),
            'recurring' => $recurring->count(),
            'latest' => SystemLog::latest('logged_at')->value('logged_at'),
        ];

        return view('admin.errors.system_logs.index', compact('logs', 'recurring', 'stats', 'level', 'search'));
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

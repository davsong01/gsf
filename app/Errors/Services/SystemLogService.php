<?php

namespace App\Errors\Services;

use App\Errors\Models\SystemLog;

class SystemLogService
{
    public function store(array $record): void
    {
        try {
            if (! in_array($record['level_name'] ?? null, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true)) {
                return;
            }

            $exception = $record['context']['exception'] ?? null;

            SystemLog::create([
                'level' => $record['level_name'] ?? 'UNKNOWN',
                'message' => $record['message'] ?? 'No message',
                'context' => $this->cleanContext($record['context'] ?? []),
                'stack_trace' => $exception instanceof \Throwable ? (string) $exception : null,
                'source' => $record['channel'] ?? 'app',
                'logged_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Logging should never break the request that is being logged.
        }
    }

    public function getRecentLogs(int $perPage = 50)
    {
        return SystemLog::latest('logged_at')->paginate($perPage);
    }

    public function getRecurringErrors(int $limit = 20)
    {
        return SystemLog::selectRaw('message, count(*) as total')
            ->whereIn('level', ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])
            ->groupBy('message')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    public function clearDatabaseLogs(): void
    {
        SystemLog::truncate();
    }

    public function deleteError(int $id): bool
    {
        $log = SystemLog::find($id);

        return $log ? (bool) $log->delete() : false;
    }

    protected function cleanContext(array $context): array
    {
        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $context['exception'] = [
                'class' => $context['exception']::class,
                'message' => $context['exception']->getMessage(),
                'file' => $context['exception']->getFile(),
                'line' => $context['exception']->getLine(),
            ];
        }

        return $context;
    }
}

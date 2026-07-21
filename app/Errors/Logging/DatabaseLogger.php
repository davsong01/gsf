<?php

namespace App\Errors\Logging;

use App\Errors\Services\SystemLogService;
use Monolog\LogRecord;

class DatabaseLogger
{
    public function __invoke($logger): void
    {
        try {
            $logger->pushProcessor(function (LogRecord $record) {
                try {
                    app(SystemLogService::class)->store([
                        'level_name' => $record->level->getName(),
                        'message' => $record->message,
                        'context' => (array) $record->context,
                        'channel' => $record->channel,
                        'datetime' => $record->datetime,
                        'extra' => (array) $record->extra,
                    ]);
                } catch (\Throwable $e) {
                    // fail silently
                }

                return $record;
            });
        } catch (\Throwable $e) {
            // fail silently
        }
    }
}

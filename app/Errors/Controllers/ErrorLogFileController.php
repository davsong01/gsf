<?php

namespace App\Errors\Controllers;

use App\Errors\Services\ErrorLogService;
use App\Http\Controllers\Controller;

class ErrorLogFileController extends Controller
{
    public function __construct(protected ErrorLogService $logService)
    {
    }

    public function index()
    {
        $files = $this->logService->getLogFiles();

        return view('admin.errors.file_logs.index', compact('files'));
    }

    public function download(string $file)
    {
        return $this->logService->download($file);
    }

    public function delete(string $file)
    {
        $this->logService->delete($file);

        return back()->with('message', "Log file '{$file}' deleted.");
    }

    public function deleteAll()
    {
        $this->logService->deleteAll();

        return back()->with('message', 'All log files deleted.');
    }
}

<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogsStakeholderAccessDenials
{
    protected function logStakeholderAccessDenied(Request $request, string $reason, array $context = []): void
    {
        $user = Auth::guard('stakeholder')->user();

        Log::warning('Stakeholder access denied.', array_merge([
            'reason' => $reason,
            'route_name' => $request->route()?->getName(),
            'route_action' => $request->route()?->getActionName(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user' => $user ? [
                'id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'name' => $user->name ?? null,
                'role_id' => $user->role_id ?? null,
                'role' => $user->role ?? null,
            ] : null,
            'impersonation' => [
                'impersonator_guard' => session('impersonator_guard') ?? session('switchuser_guard'),
                'impersonator_id' => session('impersonator_id') ?? session('switchuser'),
            ],
        ], $context));
    }
}

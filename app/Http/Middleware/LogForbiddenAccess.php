<?php

namespace App\Http\Middleware;

use Closure;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Auth\Access\AuthorizationException;

class LogForbiddenAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
        } catch (Throwable $e) {
            if ($this->isForbiddenException($e)) {
                $this->logForbidden($request, $e, $e->getCode() ?: 403, 'exception');
            }

            throw $e;
        }

        if (method_exists($response, 'getStatusCode') && (int) $response->getStatusCode() === 403) {
            $this->logForbidden($request, null, 403, 'response');
        }

        return $response;
    }

    protected function isForbiddenException(Throwable $e): bool
    {
        if ($e instanceof AuthorizationException) {
            return true;
        }

        return $e instanceof HttpExceptionInterface && (int) $e->getStatusCode() === 403;
    }

    protected function logForbidden(Request $request, ?Throwable $exception, int $statusCode, string $source): void
    {
        $route = $request->route();
        $user = Auth::guard('stakeholder')->user()
            ?? Auth::user()
            ?? Auth::guard('web')->user();

        $routeParams = collect($route?->parameters() ?? [])
            ->map(function ($parameter) {
                if (is_object($parameter)) {
                    return [
                        'class' => get_class($parameter),
                        'id' => $parameter->id ?? (method_exists($parameter, 'getKey') ? $parameter->getKey() : null),
                    ];
                }

                return $parameter;
            })
            ->all();

        Log::error('Forbidden access attempt recorded.', [
            'source' => $source,
            'status_code' => $statusCode,
            'message' => $exception?->getMessage() ?: 'Forbidden response generated without an explicit exception message.',
            'exception_class' => $exception ? get_class($exception) : null,
            'route_name' => $route?->getName(),
            'route_action' => $route?->getActionName(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user' => $user ? [
                'guard' => Auth::guard('stakeholder')->check() ? 'stakeholder' : (Auth::check() ? 'web' : null),
                'id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'role' => $user->role ?? null,
                'type' => $user->type ?? null,
            ] : null,
            'impersonation' => [
                'impersonator_guard' => session('impersonator_guard') ?? session('switchuser_guard'),
                'impersonator_id' => session('impersonator_id') ?? session('switchuser'),
            ],
            'route_params' => $routeParams,
        ]);
    }
}

<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (TokenMismatchException $e, Request $request): Response {
            $preservedInput = collect($request->except([
                '_token',
                'password',
                'password_confirmation',
                'current_password',
            ]))->toArray();

            Auth::guard('stakeholder')->logout();
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route($request->is('stakeholders*') ? 'stakeholders.loginpage' : 'login')
                ->withInput($preservedInput)
                ->with('error', 'Your session expired. Please log in again and continue.');
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

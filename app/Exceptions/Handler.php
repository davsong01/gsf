<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\TokenMismatchException;
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
            $loginRoute = $request->is('stakeholders*')
                ? route('stakeholders.loginpage')
                : route('login');

            $preservedInput = collect($request->except([
                '_token',
                'password',
                'password_confirmation',
                'current_password',
            ]))->toArray();

            return redirect()
                ->guest($loginRoute)
                ->withInput($preservedInput)
                ->with('error', 'Your session expired. Please log in again and continue.');
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

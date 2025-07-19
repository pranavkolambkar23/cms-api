<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
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
        $this->reportable(function (Throwable $e) {
            logger()->error($e);
        });
    }

    public function unauthenticated($request, AuthenticationException $exception)
    {
        // Always return JSON for API
        return response()->json([
            'message' => 'Unauthenticated. Please provide a valid token.'
        ], 401);
    }

    public function render($request, Throwable $exception)
    {
    
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }

        // Default for unhandled exceptions
        return response()->json([
            'message' => 'Something went wrong.',
            'error' => $exception->getMessage(),
        ], 500);
        

        return parent::render($request, $exception);
    }

}

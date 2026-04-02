<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


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
            //
        });

        // Validation Error
        $this->renderable(function (ValidationException $e, $request) {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        // Model Not Found
        $this->renderable(function (ModelNotFoundException $e, $request) {

            if ($request->is('api/*')) {

                $model = class_basename($e->getModel());

                return response()->json([
                    'success' => false,
                    'message' => $model . ' not foundgg'
                ], 404);
            }
        });

        // Unauthenticated
        $this->renderable(function (AuthenticationException $e, $request) {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }
        });

        // Unauthorized (Policies / Gates)
        $this->renderable(function (AuthorizationException $e, $request) {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden'
                ], 403);
            }
        });

        $this->renderable(function (HttpException $e, $request) {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Error'
                ], $e->getStatusCode());
            }
        });

        // General Error (Fallback)
        $this->renderable(function (Throwable $e, $request) {

            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server error'
                ], 500);
            }
        });
    }
}

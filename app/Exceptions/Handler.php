<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($this->isApiRequest($request)) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    private function isApiRequest($request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    private function handleApiException($request, Throwable $exception): JsonResponse
    {
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;

        $response = [
            'success' => false,
            'message' => $exception->getMessage(),
        ];
//TODO: set debugger falls for production
        if (config('app.debug')) {
            $response['exception'] = get_class($exception);
            $response['trace'] = $exception->getTrace();
        }

        return response()->json($response, $statusCode);
    }
}

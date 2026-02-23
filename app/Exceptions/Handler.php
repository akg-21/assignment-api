<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    private function handleApiException(Request $request, Throwable $exception): JsonResponse
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->modelNotFound($exception);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->notFound($exception);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->methodNotAllowed($exception);
        }

        if ($exception instanceof TooManyRequestsHttpException) {
            return $this->tooManyRequests($exception);
        }

        if ($exception instanceof HttpException) {
            return $this->httpException($exception);
        }

        return $this->genericException($exception);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
            'error_code' => 'VALIDATION_ERROR'
        ], 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
            'error_code' => 'UNAUTHENTICATED'
        ], 401);
    }

    protected function modelNotFound(ModelNotFoundException $exception): JsonResponse
    {
        $model = strtolower(class_basename($exception->getModel()));

        return response()->json([
            'success' => false,
            'message' => ucfirst($model) . ' not found',
            'error_code' => 'MODEL_NOT_FOUND'
        ], 404);
    }

    protected function notFound(NotFoundHttpException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Resource not found',
            'error_code' => 'NOT_FOUND'
        ], 404);
    }

    protected function methodNotAllowed(MethodNotAllowedHttpException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not allowed',
            'error_code' => 'METHOD_NOT_ALLOWED'
        ], 405);
    }

    protected function tooManyRequests(TooManyRequestsHttpException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
            'error_code' => 'TOO_MANY_REQUESTS'
        ], 429);
    }

    protected function httpException(HttpException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage() ?: 'HTTP error',
            'error_code' => 'HTTP_ERROR'
        ], $exception->getStatusCode());
    }

    protected function genericException(Throwable $exception): JsonResponse
    {
        if (config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'error' => 'An unexpected error occurred',
                'error_code' => 'SERVER_ERROR',
                'trace' => $exception->getTrace()
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => 'Internal server error',
            'error_code' => 'SERVER_ERROR'
        ], 500);
    }
}

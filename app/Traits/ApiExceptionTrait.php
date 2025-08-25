<?php

namespace App\Traits;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Http\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ApiExceptionTrait
{
    use ApiResponseTrait;

    /**
     * ApiExceptionTrait
     *
     *Render exception for API response.
     *
     * This trait provides a centralized way to handle and render exceptions for API responses in a consistent JSON format.
     * It covers common HTTP exceptions (authentication, authorization, not found, method not allowed, validation, etc.)
     * and returns appropriate status codes and messages. For validation errors, it returns a custom error structure. In debug mode, it can return detailed exception info.
     *
     * Usage: Use this trait in your exception handler or bootstrap to standardize API error responses.
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function renderApi($request, Throwable $e)
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR; // default 500

        // 401 Unauthorized
        if ($e instanceof AuthenticationException) {
            $status = Response::HTTP_UNAUTHORIZED;
            $message = 'Unauthorized';
        }
        // 403 Forbidden
        elseif ($e instanceof AuthorizationException ||( $e instanceof HttpException && $e->getStatusCode() === 403)) {
            $status = Response::HTTP_FORBIDDEN;
            $message = 'Forbidden';
        }
        // 405 Method Not Allowed
        elseif ($e instanceof MethodNotAllowedHttpException) {
            $status = Response::HTTP_METHOD_NOT_ALLOWED;
            $message = 'HTTP_METHOD_NOT_ALLOWED';
        }
        // 404 Not Found
        elseif ($e instanceof NotFoundHttpException) {
            $status = Response::HTTP_NOT_FOUND;
            $message = 'HTTP_NOT_FOUND';
        }
        // 422 Unprocessable Entity (Validation Error)
        elseif ($e instanceof ValidationException) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $message = 'VALIDATION_ERROR';
            $errors = $e->errors();
            return $this->errorResponse($message, $status, $errors);
        }

        // 500 Internal Server Error (default)
        else {
            $message = 'HTTP_INTERNAL_SERVER_ERROR';
            if (env('APP_DEBUG')) {

                $errors = [$e->getMessage()];
                $additional = [
                    'exception' => get_class($e),
                    'trace' => collect($e->getTrace())->take(10),
                ];

               return $this->errorResponse($message, $status, $errors, $additional);
            }

        }

        return $this->errorResponse($message, $status);
    }
}

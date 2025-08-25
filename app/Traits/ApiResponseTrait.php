<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseTrait
{
    /**
     * Return a success JSON response.
     */
    public function successResponse(
        $data = null,
        string $message = 'Request is Done Successfully',
        array $additionalParams = [],
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
            ...$additionalParams,
        ], $status);
    }

    /**
     * Return an error JSON response.
     */
    public function errorResponse(string $message = 'Error', int $status = 400, array $errors = [], array $additionalParams = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'status' => $status,
            ...$additionalParams
        ], $status);
    }
}

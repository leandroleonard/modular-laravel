<?php

namespace App\Application\Responses;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a standardized successful JSON API response.
     */
    protected function successResponse(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success'=> true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a standardized error JSON API response.
     */
    protected function errorResponse(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
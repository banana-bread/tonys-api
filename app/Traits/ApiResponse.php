<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Success',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error(string $message = null, int $code = 500): JsonResponse
    {
        return response()->json([
            'status' => 'Error',
            'code' => $code,
            'message' => $message,
            'data' => null
        ], $code);
    }
}

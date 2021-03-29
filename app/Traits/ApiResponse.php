<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function ok($data, string $message = null, int $code = 200): JsonResponse
    {
        return $this->_response('Success', $data, $message, $code);
    }

    protected function created($data, string $message = null): JsonResponse
    {
        return $this->_response('Created', $data, $message, 201);
    }

    protected function deleted($message = null): JsonResponse
    {
        return $this->_response('Created', null, $message, 204);
    }

    protected function error(string $message = null, int $code = 500): JsonResponse
    {
        return $this->_response('Error', null, $message, $code);
    }

    private function _response(string $status, $data, string $message = null, int $code)
    {
        return response()->json([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}

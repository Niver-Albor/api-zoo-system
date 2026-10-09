<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    public function successResponse($data, $message = 'Operación existosa', $code = 200)
    {
        if ($data instanceof JsonResource || $data instanceof ResourceCollection) {
            return $data->additional([
                'success' => true,
                'message' => $message,
            ])->response()->setStatusCode($code);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function errorResponse($message = 'Ocurrió un error', $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], $code);
    }
}

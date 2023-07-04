<?php

namespace App\Traits;

trait JSONResponseTrait
{
    public function successAndErrorResponse($status, $data = null, $message = null, $errorMessages = null)
    {
        if ($errorMessages != null) {
            return response()->json([
                'error' => $errorMessages
            ], $status);
        }
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $status);
    }
}

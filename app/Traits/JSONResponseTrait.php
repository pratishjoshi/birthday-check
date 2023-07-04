<?php

namespace App\Traits;

trait JSONResponseTrait
{
    public function successAndErrorResponse($status, $data = null, $message = null, $tryCatchError = null)
    {
        if ($tryCatchError != null) {
            return response()->json([
                'error' => $tryCatchError->validator->getMessageBag()->getMessages()
            ], $status);
        }
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $status);
    }
}

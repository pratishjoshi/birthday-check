<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Traits\JSONResponseTrait;

class Handler extends ExceptionHandler
{
    use JSONResponseTrait;

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
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $exception, $request) {
            if ($request->is('api/*')) {
                return $this->successAndErrorResponse(404, null, null, ['resource' => 'Resource not found']);
            }
        });
    }

    public function render($request, Exception|Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return $this->successAndErrorResponse(200, null, 'No data found', null);
        }

        if ($exception instanceof ValidationException) {
            return $this->successAndErrorResponse(422, null, null, $exception->validator->getMessageBag()->getMessages());
        }

        if ($exception instanceof AuthenticationException) {
            return $this->successAndErrorResponse(422, null, null, ['authentication' => 'Invalid credentials']);
        }

        if ($exception instanceof RouteNotFoundException) {
            return $this->successAndErrorResponse(404, null, null, ['route' => 'Route not found']);
        }

        if ($exception instanceof QueryException) {
            return $this->successAndErrorResponse(500, null, null, ['database' => 'Database error']);
        }

        if ($exception instanceof FileNotFoundException) {
            return $this->successAndErrorResponse(404, null, null, ['not_found' => 'File not found']);
        }

        if ($exception instanceof EncryptException) {
            return $this->successAndErrorResponse(422, null, null, ['encrypt' => 'Encryption error']);
        }

        if ($exception instanceof DecryptException) {
            return $this->successAndErrorResponse(422, null, null, ['decrypt' => 'Decryption error']);
        }

        return parent::render($request, $exception);
    }

    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception)) {
            Log::error($exception->getMessage());
        }

        parent::report($exception);
    }
}

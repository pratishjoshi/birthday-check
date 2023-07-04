<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Exception\BuilderNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
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
                return $this->successAndErrorResponse(404, null, 'Route not found', null);
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
            return $this->successAndErrorResponse(422, null, null, ['authentication' => 'Unauthenticated']);
        }

        if ($exception instanceof RouteNotFoundException) {
            return $this->successAndErrorResponse(404, null, null, ['route' => 'Route not found']);
        }

        return parent::render($request, $exception);
    }
}

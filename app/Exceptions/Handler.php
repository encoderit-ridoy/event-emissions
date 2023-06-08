<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Illuminate\Database\QueryException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {

            $response_code = 500;
            $message = "";
            $errors = [];

            if ($request->is('api/*')) {
                // dd($e instanceof ValidationException);
                if ($e instanceof MethodNotAllowedHttpException) {
                    $message = $e->getMessage();
                    $response_code = $e->getStatusCode();
                }
                //
                else if ($e instanceof ValidationException) {
                    $message = $e->getMessage();
                    $errors =  $e->errors();
                    $response_code = $e->status;
                }
                //
                else if ($e instanceof QueryException) {
                    $message = $e->getMessage();
                    $response_code = 503;
                }
                //
                else if ($e instanceof  \Illuminate\Auth\AuthenticationException) {
                    return $this->unauthenticated($request, $e);
                }
                //
                // else if ($e instanceof NotFoundHttpException) {
                //     $message = $e->getMessage();
                //     $response_code = $e->getResponse()->getStatusCode();
                // }
                // //
                // else if ($e->getMessage() == "") {
                //     $message = $e->getResponse()->statusText() ?? 'Resource not found';
                //     $response_code = $e->getResponse()->getStatusCode() ?? 500;
                // }
                //
                else {
                    $message = $e->getMessage();
                }

                return response()->json([
                    'message' => $message,
                    'errors' => $errors
                ], $response_code);
            }

            return parent::render($request, $e);
        });
    }
}

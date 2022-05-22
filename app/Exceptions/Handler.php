<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * @var array
     */
    protected $dontReport = [
        AccessDeniedException::class,
        NotFoundHttpException::class,
        MethodNotAllowedHttpException::class,
    ];

    /**
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        $responseStatus = 'error';
        $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $responseMessage = $exception->getMessage();

//        return response($exception->xdebug_message);

        switch (true) {
            case $exception instanceof NotFoundHttpException:
                $responseStatus = 'method_not_found';
                $responseCode = Response::HTTP_NOT_FOUND;
                $responseMessage = 'Method not found';
                break;

            case $exception instanceof MethodNotAllowedHttpException:
                $responseStatus = 'method_not_allowed';
                $responseCode = Response::HTTP_NOT_FOUND;
                break;

            case $exception instanceof ModelNotFoundException:
                $responseStatus = 'data_not_found';
                $responseCode = Response::HTTP_NOT_FOUND;
                break;

            case $exception instanceof AppException:
                $responseStatus = $exception->responseStatus;
                $responseCode = $exception->responseCode;
                break;
        }

        return response()->json([
            'status' => $responseStatus,
            'message' => $responseMessage,
        ], $responseCode);

//         return parent::render($request, $exception);
    }
}

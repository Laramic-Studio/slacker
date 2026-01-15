<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): Response|\Symfony\Component\HttpFoundation\Response
    {
        if($request->wantsJson() || $request->is('api/*')) return $this->handleApiException($request, $e); //add Accept: application/json in request

        return parent::render($request, $e);
    }

    protected function handleApiException($request, Throwable $e): JsonResponse
    {
        $exceptionInstance = get_class($e);
        $isNotClientError = false;

        switch ($exceptionInstance) {
            case AuthenticationException::class:
            case AuthorizationException::class:
                $status = Response::HTTP_UNAUTHORIZED;
                $message = $e->getMessage();
                break;
            case TokenExpiredException::class:
            case TokenInvalidException::class:
            case JWTException::class:
                $status = Response::HTTP_UNAUTHORIZED;
                $message = 'Unauthorized. Kindly login again.';
                break;
            case AuthorizationException::class|AccessDeniedHttpException::class:
                $status = Response::HTTP_FORBIDDEN;
                $message = !empty($e->getMessage()) ? $e->getMessage() : 'Forbidden';
                break;
            case \RuntimeException::class:
                $status = Response::HTTP_LOCKED;
                $message = $e->getMessage();
                break;
            case MethodNotAllowedHttpException::class:
                $status = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = 'Method not allowed';
                break;
            case NotFoundHttpException::class:
            case ModelNotFoundException::class:
                $status = Response::HTTP_NOT_FOUND;
                $message = 'The requested resource was not found';
                break;
            // case MaintenanceModeException::class:
            //     $status = Response::HTTP_SERVICE_UNAVAILABLE;
            //     $message = 'The API is down for maintenance';
            //     $isNotClientError = true;
            //     break;
            case QueryException::class:
                $status = Response::HTTP_BAD_REQUEST;
                $message = 'Internal error';
                break;
            case ThrottleRequestsException::class:
                $status = Response::HTTP_TOO_MANY_REQUESTS;
                $message = 'Too many Requests';
                break;
            case ValidationException::class:
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = $e->getMessage();
                $errors = collect($e->validator->getMessageBag()->toArray())
                    ->map(fn($messages) => $messages[0])
                    ->toArray();
                break;
            case ClientErrorException::class:
                $status = $e->getCode() != 0 ? $e->getCode() : Response::HTTP_BAD_REQUEST;
                $message = $e->getMessage();
                break;
            default:
                $status = $e->getCode() != 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
                $message = "Something went wrong internally. Kindly try again later";
                $isNotClientError = true;
                break;
        }

        if (!empty($status) && !empty($message)) {
            if($isNotClientError) {
                $errors = "An error occurred
                        \n Status:: {$status}
                        \n Message:: {$e->getMessage()}
                        \n File:: {$e->getFile()}
                        \n Line:: {$e->getLine()}
                        \n URL:: {$request->fullUrl()} \n";
            }

            return $this->responseWithError(message: $message, error: $errors ?? $message, code: $status);
        }

        return $this->respondWithNoContent();
    }

}

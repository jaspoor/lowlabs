<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
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
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedHttpException) {
            $exception = new ApplicationException('invalid_token', 401, $exception);
        }
        
        if ($exception instanceof ApplicationException &&
        $request->wantsJson()) {

            $errors = config('errors');            
            return response()->json([
                'code' => $errors[$exception->errorKey]['code'],
                'message' => $errors[$exception->errorKey]['message']
            ], $exception->httpCode);
        }

        if ($this->shouldntReport($exception)) {
            return parent::render($request, $exception);
        }
    
        // This will replace our 404 response with
        // a JSON response.
        if ($exception instanceof ModelNotFoundException &&
            $request->wantsJson()) 
        {
            return response()->json([
                'error' => 'Resource not found'
            ], 404);
        }

        // Customize the rendering for production environment
    
        $message = $exception->getMessage();

        $shortenedTrace = collect($exception->getTrace())->map(function ($trace) {
            return Arr::except($trace, ['args']);
        })->take(1);

        $traceString = collect($shortenedTrace)->map(function ($trace) {
            return Str::limit(json_encode($trace), 200);
        })->implode(PHP_EOL);

        Log::error("Exception: $message\n$traceString");

        return parent::render($request, $exception);
    }
}

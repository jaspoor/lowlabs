<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

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

<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Check if the exception is an HttpException
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();

            // Log the error for critical status codes
            if (in_array($statusCode, [500, 503, 429])) {
                Log::error("HTTP Exception: {$statusCode}", [
                    'url' => $request->url(),
                    'message' => $exception->getMessage(),
                ]);
            }

            // Return the custom error view based on the status code
            switch ($statusCode) {
                case 400: // Bad Request
                    return response()->view('others.error_pages.error_page1', [], 400);
                case 401: // Unauthorized
                    return response()->view('others.error_pages.error_page2', [], 401);
                case 403: // Forbidden
                    return response()->view('others.error_pages.error_page3', [], 403);
                case 404: // Not Found
                    return response()->view('others.error_pages.error_page5', [], 404);
                case 408: // Request Timeout
                    return response()->view('others.error_pages.error_page408', [], 408);
                case 429: // Too Many Requests
                    return response()->view('others.error_pages.error_page429', [], 429);
                case 500: // Internal Server Error
                    return response()->view('others.error_pages.error_page4', [], 500);
                case 503: // Service Unavailable
                    return response()->view('others.error_pages.error_page503', [], 503);
                default: // For unhandled HTTP status codes
                    return response()->view('others.error_pages.generic_error', ['statusCode' => $statusCode], $statusCode);
            }
        }

        // For non-HttpExceptions or unhandled exceptions
        return parent::render($request, $exception);
    }
}

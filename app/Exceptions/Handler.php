<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Get an error message for an HTTP exception.
     * @param HttpException $e
     * @return string Tailored error message for each HTTP error.
     */
    public static function getMessage(HttpException $e) {
        switch ($e->getStatusCode()) {
            case 404:
                return 'This page does not exist.';
            case 403:
                return "You don't have permission to see this content.";
            case 500:
                return "Oops. Server error!";
        }
        return 'An error occurred';
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (HttpException $e, $request) {
            return response()->view('errors.default', ['exception' => $e, 'message' => $this->getMessage($e)]);
        });
    }

}

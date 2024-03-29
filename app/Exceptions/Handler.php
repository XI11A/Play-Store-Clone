<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // return parent::render($request, $e);
        if ($e instanceof ModelNotFoundException) {
            // ajax 404 json feedback
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Not Found'], 404);
            }
            // normal 404 view page feedback
            return response()->view('errors.404', [], 404);
        }

        if ($e instanceof \PDOException || $e instanceof \QueryException) {
            return response()->view('errors.500', ['is_db_error' => true,'message' => $e->getMessage()]);
        }

        if ( ! config('app.debug') && ! $this->isHttpException($e)) {

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'We are sorry but your request contains bad syntax and cannot be fulfilled..'], 500);
            }

            return response()->view('errors.500', [], 500);
        }
        return parent::render($request, $e);
    }
}

<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\Traits\ApiException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiException;
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            return $this->getJsonException($request, $exception);
        }
        /*
        if ($request->is('store/*')) {
            return $this->getJsonException($request, $exception);
        }
        if ($request->is('market/*')) {
            return $this->getJsonException($request, $exception);
        }
        if ($request->is('portal/*')) {
            return $this->getJsonException($request, $exception);
        }
        if ($request->is('school/*')) {
            return $this->getJsonException($request, $exception);
        }
        */
        return parent::render($request, $exception);
    }
}

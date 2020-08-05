<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;

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
    public function render($request, Exception $exception)
    {
        $response = $this->handleException($request, $exception);
        // return config('app.degub') ? parent::render($request, $exception) : $this->errorResponse('Falla inesperada, intente luego', 500);
        app(CorsService::class)->addActualRequestHeaders($response, $request);

        return $response;
    }

    public function handleException($request, Exception $exception)
    {
        if($exception instanceof ValidationException)
        {
            $this->convertValidationExceptionToResponse($exception, $request);
        }
        if($exception instanceof ModelNotFoundException)
        {
            $model = class_basename($exception->getModel());
            return $this->errorResponse("No existe ninguna instancia de {$model} con el id especificado", 404);
        }
        if($exception instanceof AuthenticationException)
        {
            return $this->unauthenticated($request, $exception);
        }
        if($exception instanceof AuthorizationException)
        {
            return $this->errorResponse('No posee permisos para ejecutar esta acción', 403);
        }
        if($exception instanceof NotFoundHttpException)
        {
            return $this->errorResponse('No se encontró la URL especificada', 404);
        }
        if($exception instanceof MethodNotAllowedHttpException)
        {
            return $this->errorResponse('El método especificado en la petición no es válido', 405);
        }
        if($exception instanceof HttpException)
        {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
        if($exception instanceof QueryException)
        {
            $codigo = $exception->errorInfo[1];
            if($codigo == 1451)
            {
                return $this->errorResponse('No se puede eliminar de forma permanente el recurso porque está relacionado con algún otro.', 409);
            }
        }
        if($exception instanceof TokenMismatchException)
        {
            return redirect()->back()->withInput($request->input());
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->isFrontend($request) ? redirect()->guest('login') : $this->errorResponse('No autenticado.', 401);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        if($this->isFrontend($request))
        {
            return $request->ajax() ? response()->json($errors, 422) :
            redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        return $this->errorResponse($errors, 422);
    }

    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}

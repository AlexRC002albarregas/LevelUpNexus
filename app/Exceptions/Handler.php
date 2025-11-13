<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Campos que nunca se guardan en sesión tras errores de validación.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra callbacks personalizados para el manejo de excepciones.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
        });
    }
}

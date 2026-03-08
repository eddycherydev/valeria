<?php
namespace Core\Contracts;

/**
 * Contrato para middlewares del framework.
 * Quien no pase la validación debe enviar respuesta (header/body) y terminar con exit.
 * Quien pase puede no hacer nada; el request continúa al controlador.
 */
interface MiddlewareInterface
{
    public function handle(): void;
}

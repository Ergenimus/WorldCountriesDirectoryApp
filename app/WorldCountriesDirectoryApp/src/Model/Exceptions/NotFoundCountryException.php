<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

// NotFoundCountryException - исключение не найденной страны
class NotFoundCountryException extends Exception {

    // переопределение конструктора исключения
    public function __construct($notFoundCode, Throwable $previous = null) {
        $exceptionMessage = "Страна с кодом '". $notFoundCode ."' не найдена.";
        // вызов конструктора базового класса исключения
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::NOT_FOUND_ERROR,
            previous: $previous,
        );
    }
}
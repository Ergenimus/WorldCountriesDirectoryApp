<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

// InvalidCountryCodeException - исключение невалидного кода страны
class InvalidCountryCodeException extends Exception {

    // переопределение конструктора исключения
    public function __construct($invalidCode, $message, Throwable $previous = null) {
        $exceptionMessage = "Страна со значением '". $invalidCode ."' не валидна: ".$message;
        // вызов конструктора базового класса исключения
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::INVALID_CODE_ERROR,
            previous: $previous,
        );
    }
}

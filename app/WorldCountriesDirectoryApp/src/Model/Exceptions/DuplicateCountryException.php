<?php

namespace App\Model\Exceptions;

use Throwable;
use Exception;

// DuplicatedCountryException - исключение дублирующегося кода страны
class DuplicateCountryException extends Exception {

    // переопределение конструктора исключения
    public function __construct(string $duplicatedCode, Throwable $previous = null) {
        $exceptionMessage = "Страна со значением '". $duplicatedCode ."' уже существует.";
        // вызов конструктора базового класса исключения
        parent::__construct(
            message: $exceptionMessage, 
            code: ErrorCodes::DUPLICATED_CODE_ERROR,
            previous: $previous,
        );
    }
}
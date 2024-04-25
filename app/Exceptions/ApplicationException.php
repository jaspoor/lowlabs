<?php

namespace App\Exceptions;

class ApplicationException extends \Exception {

    public $errorKey;
    public $httpCode;

    public function __construct(string $errorKey, int $httpCode = 400, \Throwable $previous = null) {
        parent::__construct("", 0, $previous);
        $this->errorKey = $errorKey;        
        $this->httpCode = $httpCode;
    }
}
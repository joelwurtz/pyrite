<?php

namespace Fibo\Router\Validation;

class ValidationException extends \RuntimeException
{
    
    private $errors = array();
    
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
}
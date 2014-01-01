<?php

namespace Fibo\Router\Validation\Parameter;

use Fibo\Router\Validation\ParameterValidator;

class Email implements ParameterValidator
{
    /**
     * (non-PHPdoc) @see \Fibo\Router\Validation\ParameterValidator::validate()
     */
    public function validate($value)
    {
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
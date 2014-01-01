<?php

namespace Fibo\Router\Validation\Parameter;

use Fibo\Router\Validation\ParameterValidator;

class Int implements ParameterValidator
{

    public function validate($value)
    {
        if (! is_numeric($value)) {
            return false;
        }
        
        if ((int) $value != $value) {
            return false;
        }
        
        return true;
    }
}
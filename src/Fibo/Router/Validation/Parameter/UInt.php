<?php

namespace Fibo\Router\Validation\Parameter;

use Fibo\Router\Validation\ParameterValidator;

class UInt extends Int
{

    public function validate($value)
    {
        if (! parent::validate($value)) {
            return false;
        }
        
        return (int) $value >= 0;
    }
}
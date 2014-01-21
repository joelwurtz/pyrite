<?php

namespace Pyrite\Stack\Validation\Parameter;

use Pyrite\Stack\Validation\ParameterValidator;

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
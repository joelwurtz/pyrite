<?php

namespace Pyrite\Stack\Validation\Parameter;

use Pyrite\Stack\Validation\ParameterValidator;

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
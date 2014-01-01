<?php

namespace Fibo\Router\Validation\Parameter;

use Fibo\Router\Validation\ParameterValidator;

class NotNullOrEmpty implements ParameterValidator
{

    public function validate($value)
    {
        if ($value === null) {
            return false;
        }
        
        if (empty($value)) {
            return false;
        }
        
        if (strlen(trim($str)) == 0) {
            return false;
        }
        
        return true;
    }
}
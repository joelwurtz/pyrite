<?php

namespace Pyrite\Stack\Validation\Parameter;

use Pyrite\Stack\Validation\ParameterValidator;

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
        
        if (strlen(trim($value)) == 0) {
            return false;
        }
        
        return true;
    }
}
<?php

namespace Pyrite\Stack\Validation\Parameter;

use Pyrite\Stack\Validation\ParameterValidator;

class Email implements ParameterValidator
{
    /**
     * (non-PHPdoc) @see \Pyrite\Stack\Validation\ParameterValidator::validate()
     */
    public function validate($value)
    {
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
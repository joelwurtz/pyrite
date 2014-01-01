<?php

namespace Fibo\Router\Validation;

class ParameterValidationException extends ValidationException
{
    private $parameterName;
    
    private $validationKey;
    
    public function __construct($parameterName, $validationKey)
    {
        parent::__construct(array('Invalid parameter value.'));
        
        $this->parameterName = $parameterName;
        $this->validationKey = $validationKey;
    }
    
    public function getParameterName()
    {
        return $this->parameterName;
    }
    
    public function getValidationKey()
    {
        return $this->validationKey;
    }
}
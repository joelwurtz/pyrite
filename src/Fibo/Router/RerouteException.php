<?php

namespace Fibo\Router;

class RerouteException extends RedirectException
{
    
    private $params;
    
    public function __construct($path, array $params)
    {
        parent::__construct($path);
        
        $this->params = $params;
    }
    
    public function getParams()
    {
        return $this->params;
    }
    
}
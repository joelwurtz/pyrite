<?php

namespace Fibo\Router;

class RedirectException extends \Exception
{
    
    private $path;
    
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
}
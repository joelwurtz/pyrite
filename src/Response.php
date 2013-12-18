<?php

namespace Fibo\Router;

interface Response 
{
    function render(array $data);
    
    function setLayout(Layout $layout);
    
    function setViewFile($viewName);
}
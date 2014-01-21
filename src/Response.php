<?php

namespace Pyrite\Stack;

interface Response 
{
    function render(array $data);
    
    function setLayout(Layout $layout);
    
    function setViewFile($viewName);
}
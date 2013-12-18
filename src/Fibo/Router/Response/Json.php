<?php
namespace Fibo\Router\Response;

use Fibo\Router\Response;
use Fibo\Router\Layout;
use Fibo\Router\View;

class Json implements Response
{

    private $file;

    private $layout;

    public function render(array $data)
    {
        if (trim($this->file) == '') {
            return json_encode($data);
        }
        
        $view = new View($this->file);
        $view->assignx($data);
        
        return $view->compile();
    }

    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function setViewFile($viewName)
    {
        $this->file = $viewName;
    }
}
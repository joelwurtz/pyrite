<?php
namespace Pyrite\Stack\Response;

use Pyrite\Stack\Response;
use Pyrite\Stack\View;
use Pyrite\Stack\Layout;

class Html implements Response
{
    private $file;
    
    private $layout;
    
    public function render(array $data)
    {
        $view = new View($this->file);
        $view->assignx($data);
        
        $viewData = $view->compile();
        
        $this->layout->assignx($data);
        $this->layout->assign('view', $viewData);
        
        return $this->layout->compile();
    }
    
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
    }
    
    public function setViewFile($file)
    {
        $this->file = $file;
    }
}
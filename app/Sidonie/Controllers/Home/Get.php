<?php

namespace Sidonie\Controllers\Home;

use Pyrite\Stack as Stack;
use Stack\Pyrite\Router as Router;

class Get extends Router\ControllerAbstract implements Router\Controller {

    protected function executeAction() {
        $this->setData('monArray', array('foo', 'bar'));
        return self::FAILURE;
    }
}
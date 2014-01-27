<?php

namespace Sidonie\Controllers\Home;

use Pyrite\Stack as Stack;

class Post extends Stack\Controller {
    const MY_RETURN_SUCCESS = "tamere";
    const MY_RETURN_SUCCESS2 = "tamere2";


    protected function executeAction() {
        var_dump($this->getRequest()->getAllParams());

        $this->setData('pouet', 'pouet_value');


        return static::MY_RETURN_SUCCESS;
    }

    public function preRunMethod() {

    }
}
<?php

namespace Stack\Pyrite\Router\Plugin\Dispatch;

use Stack\Pyrite\Router\Plugin;

class Controller extends Plugin\StackElementAbstract  implements Plugin\StackElement {
    public function invoke() {
        $controller = $this->container->get($this->pluginData);
        $ret = $controller->execute();

        $controllerDatas = $controller->getAllDatas();
        foreach($controllerDatas as $key => $value) {
            $this->dispatchResponse->setData($key, $value);
        }

        $this->dispatchResponse->setResult($ret);
        return $ret;
    }
}
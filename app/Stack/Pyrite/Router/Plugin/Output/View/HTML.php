<?php

namespace Stack\Pyrite\Router\Plugin\Output\View;

use Stack\Pyrite\Router\Plugin;
use Stack\Pyrite\Router\Plugin\Output as Output;

class HTML extends Plugin\StackElementAbstract  implements Plugin\StackElement {
    public function invoke() {

        $result = $this->dispatchResponse->getResult();
        if (array_key_exists($result, $this->pluginData)) {
            $path = ROOT_DIR . '/' . $this->pluginData[$result];
            if (file_exists($path)) {
                $view = new Output\View($path, $this->dispatchResponse->getAllDatas());
                ob_start();
                $view->render();
                $res = ob_get_clean();
                return $res;
            }
        }
    }
}
<?php

namespace Stack\Pyrite\Router\Plugin\Dispatch;

use Stack\Pyrite\Router\Plugin;

class ACL extends Plugin\StackElementAbstract  implements Plugin\StackElement {
    public function invoke() {
        if (true) {
            return $this->wrappedElement->invoke();
        }
    }
}
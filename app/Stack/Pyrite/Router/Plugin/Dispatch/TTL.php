<?php

namespace Stack\Pyrite\Router\Plugin\Dispatch;

use Stack\Pyrite\Router\Plugin;

class TTL extends Plugin\StackElementAbstract implements Plugin\StackElement {
    public function invoke() {
        if ($this->pluginData == "is_in_cache") {
            $this->dispatchResponse->setResult('content_loaded_from_cache');
            return 'content_loaded_from_cache';
        }
        else {
            $this->dispatchResponse->setData('ttl1', 'P3D');
            $res = $this->wrappedElement->invoke();
            $this->dispatchResponse->setData('ttl2', 'P3D');
        }
    }
}
<?php

namespace Stack\Pyrite\Router\Plugin;

use Stack\Pyrite\Router;

abstract class StackElementAbstract implements StackElement {
    protected $wrappedElement = null;
    protected $container = null;
    protected $pluginData = array();
    protected $request = null;
    protected $dispatchResponse = null;

    final public function __construct(StackElement $wrappedElement = null) {
        $this->wrappedElement = $wrappedElement;
    }

    public function setPluginData($data) {
        $this->pluginData = $data;
        return $this;
    }

    public function setContainer($container) {
        $this->container = $container;
        return $this;
    }

    public function setRequest($request) {
        $this->request = $request;
        return $this;
    }

    public function setDispatchResponse(Router\DispatchResponse $response) {
        $this->dispatchResponse = $response;
    }

    abstract public function invoke();
}
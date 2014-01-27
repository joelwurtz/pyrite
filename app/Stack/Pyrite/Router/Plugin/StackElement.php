<?php

namespace Stack\Pyrite\Router\Plugin;

use Stack\Pyrite\Router;

interface StackElement {
    function invoke();
    function setContainer($container);
    function setPluginData($data);
    function setRequest($request);
    function setDispatchResponse(Router\DispatchResponse $response);
}
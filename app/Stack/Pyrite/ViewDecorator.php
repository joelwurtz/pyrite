<?php

namespace Stack\Pyrite;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewDecorator implements HttpKernelInterface {
    private $decoratedKernel = null;
    private $silexApplication = null;

    public function __construct($decoratedKernel, $silexApplication) {
        $this->decoratedKernel = $decoratedKernel;
        $this->silexApplication = $silexApplication;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
        $response = $this->decoratedKernel->handle($request, $type, $catch);
        return new Response("c moi qui decide");
    }
}
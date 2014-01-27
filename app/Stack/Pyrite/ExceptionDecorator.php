<?php

namespace Stack\Pyrite;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionDecorator implements HttpKernelInterface {
    private $decoratedKernel = null;
    private $silexApplication = null;

    public function __construct($decoratedKernel, $silexApplication) {
        $this->decoratedKernel = $decoratedKernel;
        $this->silexApplication = $silexApplication;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
        try {
            return $this->decoratedKernel->handle($request, $type, true);
        }
        catch(\Exception $e) {
            header("Status: 404 Not Found", true, 404);
            return new Response($e->getMessage());
        }
    }
}
<?php

namespace Fibo\Router;

use Symfony\Component\HttpFoundation\Request;

class Redirect
{

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @var \Silex\Application
     */
    private $app;

    public function __construct($app, $redirect)
    {
        $this->app = $app;
        $this->redirect = $redirect;
    }

    public function redirect($path)
    {
        throw new \Fibo\Router\RerouteException($path);
    }

    public function route($path)
    {
        throw new \Fibo\Router\RerouteException($path);
    }
}
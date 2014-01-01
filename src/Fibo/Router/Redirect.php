<?php

namespace Fibo\Router;

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
        throw new \Fibo\Router\RedirectException($path);
    }

    public function route($path, array $params = array())
    {
        throw new \Fibo\Router\RerouteException($path, $params);
    }
}
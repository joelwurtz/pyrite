<?php

namespace Pyrite\Stack;

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
        throw new \Pyrite\Stack\RedirectException($path);
    }

    public function route($path, array $params = array())
    {
        throw new \Pyrite\Stack\RerouteException($path, $params);
    }
}
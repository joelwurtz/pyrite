<?php
namespace Pyrite\Stack;

use Symfony\Component\HttpFoundation\Request;
use Pyrite\Stack\Layout\Selector;
use Pyrite\Stack\Layout\Builder;
use Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Global application object providing dependency injection for controllers
 * @author thibaud
 *
 */
class Application
{

    /**
     *
     * @var \Silex\Application
     */
    private $app;

    /**
     *
     * @var \DICIT\Container
     */
    private $container;

    /**
     *
     * @var \Symfony\Component\Yaml\Yaml
     */
    private $yaml;

    /**
     *
     * @var string
     */
    private $routeFile = '';

    /**
     *
     * @var array
     */
    private $data = null;

    /**
     *
     * @var array
     */
    private $routeData;

    /**
     *
     * @var Layout\Selector
     */
    private $layoutSelector;

    /**
     *
     * @var Request
     */
    private $request = null;

    /**
     * Initialize a new instance.
     *
     * @param \Silex\Application $application
     * @param \DICIT\Container $container
     * @param \Symfony\Component\Yaml\Yaml $yaml Yaml parser used for routes.
     */
    public function __construct(\Silex\Application $application,\DICIT\Container $container,\Symfony\Component\Yaml\Yaml $yaml)
    {
        $this->app = $application;
        $this->container = $container;
        $this->yaml = $yaml;
    }

    /**
     * Delegates all calls to underlying Silex application object
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->app, $name), $args);
    }

    /**
     * Sets the file name that contains the route definitions.
     * @param string $filename
     * @throws \RuntimeException when $filename does not exist.
     */
    public function setRouteFile($filename)
    {
        if (! file_exists($filename)) {
            throw new \RuntimeException(sprintf('Route file not found ["%s"]', $filename));
        }

        $this->routeFile = $filename;
    }

    /**
     * Returns the current request object
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the current request object. This method has no effect if called after run().
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns the default error path.
     * @return string
     */
    public function getErrorPath()
    {
        return '/error';
    }

    /**
     * Returns a boolean stating whether the application is in debug mode.
     * @return boolean
     */
    public function isDebug()
    {
        return $this->app['debug'] == true;
    }

    /**
     * Returns the appropriate response selector for a given route.
     * @param string $routeName
     * @return \Pyrite\Stack\ResponseSelector
     */
    public function getResponseSelector($routeName)
    {
        $routeData = $this->getRouteData($routeName);
        $outputData = $this->extractValue($routeData, 'output', array());
        $selector = new ResponseSelector();

        foreach ($outputData as $name => $output) {
            $view = $this->extractValue($output, 'view', '');
            $layout = $this->extractValue($output, 'layout', '');

            $selector->addOutput($name, $layout, $view, $this->extractValue($routeData, 'scripts'));
        }

        $selector->setRootDirectory(ROOT_DIR);
        $selector->setLayoutSelector($this->getLayoutSelector($routeName));

        return $selector;
    }

    /**
     * Returns a layout selector instance.
     * @return \Pyrite\Stack\Layout\Selector
     */
    public function getLayoutSelector()
    {
        $this->loadData();

        $builder = new Builder();
        $builder->setRootDirectory(ROOT_DIR);

        $layoutSelector = new Selector();
        $layoutSelector->setBuilder($builder);

        foreach ($this->data['layouts'] as $layout => $data) {
            $layoutSelector->addLayout($layout, $data);
        }

        $layoutSelector->setDefaultLayout($this->data['default-layout']);

        return $layoutSelector;
    }

    public function getControllerHooks($routeName, $outputName)
    {
        $hooks = new ControllerHookCollection();
        $routeData = $this->getRouteData($routeName);
        $outputData = $this->extractValue($routeData, 'output', array());

        foreach ($outputData as $output => $data) {
            $before = $this->extractValue($data, 'before-run', array()) ?: array();
            $after = $this->extractValue($data, 'after-run', array()) ?: array();

            $hooks->setBeforeHooks($output, $before);
            $hooks->setAfterHooks($output, $after);
        }

        return $hooks;
    }

    /**
     * Registers all routes and runs the application.
     */
    public function run()
    {
        $this->registerRoutes();
        $this->setRequest(Request::createFromGlobals());

        $this->app->run($this->getRequest());
    }

    public function reroute($path, array $params)
    {
        $subRequest = Request::create($path, $this->request->getMethod(), $params, $_COOKIE, $_FILES, $_SERVER);

        if ($session = $this->request->getSession()) {
            $subRequest->setSession($session);
        }

        return $this->app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }

    public function redirect($path)
    {
        return $this->app->redirect($path);
    }

    private function getRouteData($routeName)
    {
        if (! array_key_exists($routeName, $this->routeData)) {
            throw new \RuntimeException(sprintf('Route does not exist [%s]', $routeName));
        }

        return $this->routeData[$routeName];
    }

    private function loadData()
    {
        if ($this->data === null) {
            if (trim($this->routeFile) == '') {
                throw new \RuntimeException('Route file is not set.');
            }

            $this->data = $this->loadDataRecursive($this->routeFile);
        }
    }

    private function loadDataRecursive($filePath)
    {
        $yml = array();
        $dirname = dirname($filePath);
        $res = $this->yaml->parse($filePath);

        foreach($res as $key => $value) {
            if ($key == 'include') {
                foreach($value as $file) {
                    $subYml = $this->loadDataRecursive($dirname . '/' . $file);
                    $yml = array_merge_recursive($yml, $subYml);
                }
            }
            else {
                $yml = array_merge_recursive($yml, array($key => $res[$key]));
            }
        }

        return $yml;
    }

    private function registerRoutes()
    {
        $this->loadData();
        $routes = $this->data['routes'];

        foreach ($routes as $name => $route) {
            $this->registerRoute($name, $route);
        }

        $this->routeData = $routes;
    }

    private function registerRoute($routeName, array $route)
    {
        $app = $this;
        $container = $this->container;
        $pattern = $app->extractValue($route, 'pattern');
        $controllerName = $app->extractValue($route, 'controller');

        $to = function () use($app, $container, $routeName, $controllerName)
        {
            try {
                $request = new \Pyrite\Stack\Request($app->getRequest());

                $controller = $container->get($controllerName);
                $controller->setRequest($request);
                $controller->setRedirect(new Redirect($app, $request));

                $responseSelector = $app->getResponseSelector($routeName);
                $outputName = $responseSelector->getOutputName($request);

                /* @var $hooks  \Pyrite\Stack\ControllerHookCollection */
                $hooks = $app->getControllerHooks($routeName, $outputName);

                $hooks->runBefore($controller, $outputName);
                $controller->execute();
                $hooks->runAfter($controller, $outputName);

                $response = $responseSelector->getResponse($request);

                return $response->render($controller->getData());
            }
            catch (\Pyrite\Stack\RerouteException $ex) {
                return $app->reroute($ex->getPath(), $ex->getParams());
            }
            catch (\Pyrite\Stack\RedirectException $ex) {
                return $app->redirect($ex->getPath());
            }
            catch (\Exception $ex) {
                if (! $app->isDebug()) {

                    error_log($ex->getMessage());

                    return $app->reroute($app->getErrorPath(), array());
                }

                throw $ex;
            }
        };

        $methods = $this->extractValue($route, 'methods');
        foreach ($methods as $method) {
            $this->registerMethod($method, $routeName, $pattern, $to);
        }
    }

    private function registerMethod($method, $name, $pattern, $to)
    {
        $method = trim($method);
        if (in_array($method, array('get', 'post', 'put', 'delete')) !== false) {
            $this->app->{$method}($pattern, $to);
        }
    }

    private function extractValue(array $data, $path, $default = null)
    {
        if (strpos($path, '.') !== false) {
            $parts = explode('.', $path, 1);

            if (! array_key_exists($parts[0], $data)) {
                return $default;
            }

            return $this->extractValue($data[$parts[0]], $parts[1]);
        }

        if (! array_key_exists($path, $data)) {
            return $default;
        }

        return $data[$path];
    }

    private function validateArrayKey(array $data, $key)
    {
        if (! array_key_exists($key, $data)) {
            throw new \RuntimeException(sprintf('Invalid configuration : could not find required element [%s]', $key));
        }
    }
}
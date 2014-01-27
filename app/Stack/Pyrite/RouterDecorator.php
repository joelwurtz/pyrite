<?php

namespace Stack\Pyrite;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RouterDecorator implements HttpKernelInterface {
    private $decoratedKernel = null;
    private $silexApplication = null;
    private $controllerBuilder = null;
    private $yaml = null;

    private $data = null;
    private $routeFile = null;

    public function __construct($decoratedKernel, $silexApplication, $yaml, $controllerBuilder) {
        $this->decoratedKernel = $decoratedKernel;
        $this->silexApplication = $silexApplication;
        $this->yaml = $yaml;
        $this->routeFile = ROOT_DIR . '/config/routes.yml';
        $this->controllerBuilder = $controllerBuilder;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
        $this->loadRoutes($request);
        return $this->decoratedKernel->handle($request, $type, $catch);
    }

    private function loadRoutes(REquest $request) {
        // chargement du YML
        $this->loadData();
        $routes = $this->data['routes'];

        foreach($routes as $routeName => $routeMetadata) {
            $pattern = $this->extractValue($routeMetadata, 'route.pattern');
            $methods = $this->extractValue($routeMetadata, 'route.methods');
            $closure = $this->controllerBuilder->createControllerClosure($routeName, $routeMetadata, $request);
            foreach ($methods as $method) {
                $this->registerMethod($method, $routeName, $pattern, $closure);
            }
        }
    }

    private function makeClosure($routeName, array $routeMetadata = array()) {
        $closure = function() use($routeName, $routeMetadata) {

            // here we inject all the decorator for the controller

            // $controller->run();

            return $routeName . "\n" . var_export($routeMetadata, true);
        };

        return $closure;
    }

    private function registerMethod($method, $name, $pattern, \Closure $to) {
        $method = trim(strtolower($method));
        if (in_array($method, array('get', 'post', 'put', 'delete')) !== false) {
            $this->silexApplication->{$method}($pattern, $to);
        }
    }


    /**
     * @deprecated
     */
    private function old() {
        $container = $this->container;
        $pattern = $app->extractValue($route, 'pattern');
        $controllerName = $app->extractValue($route, 'controller');
        $methods = $this->extractValue($route, 'methods');

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

        foreach ($methods as $method) {
            $this->registerMethod($method, $routeName, $pattern, $to);
        }
    }


    private function extractValue(array $data, $path, $default = null) {
        if (strpos($path, '.') !== false) {
            $parts = explode('.', $path, 2);
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

    private function loadData() {
        if ($this->data === null) {
            if (trim($this->routeFile) == '') {
                throw new \RuntimeException('Route file is not set.');
            }

            $this->data = $this->loadDataRecursive($this->routeFile);
        }
    }

    private function loadDataRecursive($filePath) {
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
}
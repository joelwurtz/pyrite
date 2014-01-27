<?php

namespace Stack\Pyrite\Router;

use Symfony\Component\HttpFoundation\Response;

class ControllerBuilder {
    protected $container = null;

    public function __construct($container) {
        $this->container = $container;
    }

    public function createControllerClosure($routeName, array $routeMetadata = array(), $request) {
        $builder = $this;

        $closure = function() use ($routeName, $routeMetadata, $builder, $request) {
            $routeDispatch = $routeMetadata['dispatch'];
            $routeOutput = $routeMetadata['output'];

            $dispatchResponse = new DispatchResponseImpl();

            try {
                $stackedPlugins = $builder->stackPlugins($request, $routeDispatch, $dispatchResponse);
                $dispatchResult = $stackedPlugins->invoke();
            }
            catch(\Exception $e) {
                return new Response('Dispatch builder : ' . $e->getMessage(), $e->getCode());
            }

            try {
                $stackedPlugins = $builder->stackPlugins($request, $routeOutput, $dispatchResponse);
                $outputResult = $stackedPlugins->invoke();
                return new Response($outputResult, 200);
            }
            catch(\Exception $e) {
                return new Response('Output builder : ' . $e->getMessage(), $e->getCode());
            }

        };

        return $closure;

    }

    public function stackPlugins($request, array $plugins = array(), DispatchResponse $dispatchResponse) {
        $reversed = array_reverse($plugins);
        $lastInstance = null;
        foreach($reversed as $pluginName => $plugin) {
            if (strpos($pluginName, "\\") !== false) {
                if (!class_exists($pluginName)) {
                    throw new \RuntimeException(sprintf('Plugin %s not found', $pluginName), 500);
                }

                $class = new \ReflectionClass($pluginName);
                $lastInstance = $class->newInstanceArgs(array($lastInstance));
                $lastInstance->setPluginData($plugin);
                $lastInstance->setRequest($request);
                $lastInstance->setContainer($this->container);
                $lastInstance->setDispatchResponse($dispatchResponse);
            }
            else {
                throw new \RuntimeException(sprintf('Need FQCN for plugin %s', $pluginName), 500);
            }
        }
        return $lastInstance;
    }
}
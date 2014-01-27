<?php
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpFoundation\Request;

require_once 'bootstrap.php';

$app = new Silex\Application();
$app['exception_handler']->disable();



// --------- DEPENDENCY INJECTION
$injections = dirname(__DIR__) . '/config/injections.yml';
$containerConfig = new DICIT\Config\YML($injections);
$container = new DICIT\Container($containerConfig);

$yamlEngine = new Symfony\Component\Yaml\Yaml();
$controllerBuilder = new \Stack\Pyrite\Router\ControllerBuilder($container);


$stackBuilder = new Stack\Builder();
$stackBuilder->push('Stack\Pyrite\RouterDecorator', $app, $yamlEngine, $controllerBuilder);
$stackBuilder->push('Stack\Pyrite\ExceptionDecorator', $app);

$app = $stackBuilder->resolve($app);

$request = Request::createFromGlobals();
$handled = $app->handle($request);
$response = $handled->send();
$app->terminate($request, $response);







die();




$yaml = new Symfony\Component\Yaml\Yaml();

$silex = new Silex\Application();



$silex['debug'] = $container->getParameter('application.debug');
if ($silex['debug']) {
    register_shutdown_function(function() {
        echo '<h1>Shutdown error</h1>';
        var_dump(error_get_last());
    });
}

$app = new Pyrite\Stack\Application($silex, $container, $yaml);

// --------- ROUTER
$routes = dirname(__DIR__) . '/config/routes.yml';
$app->setRouteFile($routes);

// --------- GO
$app->run();
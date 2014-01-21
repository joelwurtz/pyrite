Usage :

Pyrite requires a DI container to work. Currently, only DIC-IT is supported.

You need to setup a configuration directory with your YAML files for DIC-IT.


Sample bootstrap file :


```
require_once dirname(__DIR__) . '/vendor/autoload.php';

$routes = dirname(__DIR__) . '/config/routes.yml';
$injections = dirname(__DIR__) . '/config/injections.yml';

$containerConfig = new DICIT\Config\YML($injections);
$container = new DICIT\Container($containerConfig);
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

$app->setRouteFile($routes);
$app->run();

```

Additionally, you need a YAML route file :

```
default-layout: index

layouts:
    index:
        template: app/Modules/Home/layouts/index.phtml
        stylesheets:
            - [ "//fonts.googleapis.com/css?family=Montserrat:400,700", css, screen ]
            - [ "style/style.css", css, screen ]
        scripts:
            head:
                - '/js/jquery.js'
            bottom: []

routes:
    home:
        pattern: "/"
        controller: homeController # The controller name is a reference to a DIC-IT object definition. The instance must derive from Pyrite\Router\Controller
        methods: [ get ]
        output:
            html:
                before-run: [ preRunMethod ]
                view: app/Modules/Home/views/home.phtml
```


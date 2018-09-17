<?php

require './vendor/autoload.php';
require '../ypf/vendor/autoload.php';

use Opulence\Routing\Builders\RouteBuilderRegistry;
use Opulence\Routing\Caching\FileRouteCache;
use Opulence\Routing\Regexes\Caching\FileGroupRegexCache;
use Opulence\Routing\Regexes\GroupRegexFactory;
use Opulence\Routing\RouteFactory;

$routesCallback = function (RouteBuilderRegistry $routes) {
    $routes->map('GET', 'books/:bookId')
        ->toMethod(Controller\Index::class, 'index');
};

$routeFactory = new RouteFactory(
    $routesCallback,
    new FileRouteCache('/tmp/routes.cache')
);
$regexFactory = new GroupRegexFactory(
    $routeFactory->createRoutes(),
    new FileGroupRegexCache('/tmp/regexes.cache')
);

$services = [
    'factory' => Ypf\Application\Swoole::class,

    'swoole' => [
        'listen' => '*:7000',
    ],
    'middleware' => [
        new Middleware\RouteMiddleware($regexFactory),
    ],
];
$services['logger'] = function () {
    $logger = new Monolog\Logger('test');
    $logger->pushProcessor(new Monolog\Processor\PsrLogMessageProcessor(null, true));
    $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::DEBUG));

    return $logger;
};

$app = new Ypf\Application($services);

$app->run();

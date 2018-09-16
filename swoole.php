<?php

require './vendor/autoload.php';
require '../ypf/vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/hello/{name}', function ($request) {
        //The route parameters are stored as attributes
        $name = $request->getAttribute('name');

        //You can echo the output (it will be captured and written into the body)
        return sprintf('Hello %s', str_repeat($name, mt_rand(100, 10000)));
    });
});

$services = [
    'factory' => Ypf\Application\Swoole::class,

    'swoole' => [
        'listen' => '*:7000',
    ],
    'middleware' => [
        new Middleware\BenchmarkMiddleware(),
        //new Middlewares\FastRoute($dispatcher),
        new Middlewares\RequestHandler(),
        Middleware\RewriteMiddleware::class,
    ],
    //'dispatcher' => Middleland\Dispatcher::class;
    'router' => '',
];

$app = new Ypf\Application($services);

$app->run();

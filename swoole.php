<?php

require './vendor/autoload.php';
require '../ypf/vendor/autoload.php';
$services = [
    'factory' => Ypf\Application\Swoole::class,

    'swoole' => [
        'listen' => '*:7000',
    ],
    'middleware' => [
        Middleware\BenchmarkMiddleware::class,
        Middleware\RewriteMiddleware::class,
    ],
    //'dispatcher' => Middleland\Dispatcher::class;
    'router' => '',
];

$app = new Ypf\Application($services);

$app->run();

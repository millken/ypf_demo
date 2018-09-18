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

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//Create the logger
$logger = new Logger('access');
$logger->pushHandler(new StreamHandler(fopen('./access-log.log', 'wr+')));

$services = [
    'factory' => Ypf\Application\Swoole::class,

    'swoole' => [
        'server' => [
            'address' => '127.0.0.1',
            'port' => 7000,
        ],
        'options' => [
            'task_worker_num' => 3,
            'dispatch_mode' => 1,
        ],
    ],
    'workers' => [
        [\Worker\SingleTest::class],
        [\Worker\CronTest::class, '3'],
        [\Worker\CronTest::class, '* * * * *'],
    ],
    'middleware' => [
        new Middlewares\AccessLog($logger),
        new Middleware\RouteMiddleware($regexFactory),
    ],
];
$services['db'] = function () {
    $config = [
        'dbtype' => 'pgsql',
        'host' => '172.17.0.3',
        'port' => 5432,
        'dbname' => 'ip',
        'username' => 'postgres',
        'password' => 'admin',
        'charset' => 'utf8',
        'timeout' => 3,
        'presistent' => false,
    ];
    $db = new Ypf\Database\Connection($config);

    return $db;
};
$services['logger'] = function () {
    $logger = new Monolog\Logger('test');
    $logger->pushProcessor(new Monolog\Processor\PsrLogMessageProcessor(null, true));
    $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::DEBUG));

    return $logger;
};

$app = new Ypf\Application($services);

$app->run();

<?php

require './vendor/autoload.php';
require '../ypf/vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/hello/{name}', function ($request) {
        //The route parameters are stored as attributes
        $name = $request->getAttribute('name');
        //You can echo the output (it will be captured and written into the body)
        return sprintf('Hello %s', str_repeat($name, mt_rand(100, 1000)));
    });
    $r->addRoute('GET', '/home', 'Controller\Index@index');
});

$services = [
    'factory' => Ypf\Application\Swoole::class,

    'swoole' => [
        'listen' => '*:7000',
    ],
    'middleware' => [
        new Middleware\BenchmarkMiddleware(),
        new Middlewares\FastRoute($dispatcher),
        new Middlewares\RequestHandler(),
        //Middleware\RewriteMiddleware::class,
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

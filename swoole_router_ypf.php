<?php

require './vendor/autoload.php';
require '../ypf/vendor/autoload.php';
use GuzzleHttp\Psr7\Response;

$router = new Ypf\Route\Router();
$router->map('GET', '/', function ($request) {
    $session = $request->getAttribute('session');

    return 'Receive '.$session->get('name', '^_^');
});
$router->get('/hello/{name}?', function ($request) {
    $session = $request->getAttribute('session');
    $name = ucwords($request->getAttribute('name', 'World!'));
    $session->set('name', $name);

    return new Response(200, [], 'hello '.$name);
});

$services = [
    'factory' => Ypf\Application\Swoole::class,

    'swoole' => [
        'server' => [
            'address' => '*',
            'port' => 8080,
        ],
        // 'options' => [
        //     'task_worker_num' => 3,
        //     'dispatch_mode' => 1,
        // ],
    ],
    'middleware' => [
        new Zend\Expressive\Session\SessionMiddleware(new Zend\Expressive\Session\Ext\PhpSessionPersistence()),
        new Ypf\Route\Middleware($router),
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

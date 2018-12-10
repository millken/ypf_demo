<?php

require './vendor/autoload.php';
require '../ypf/vendor/autoload.php';
use GuzzleHttp\Psr7\Response;

$router = new Ypf\Route\Router();
$router->map('GET', '/', function ($request) {
    // $session = $request->getAttribute('session');
    $name = $_SESSION['name'];
    //$name = isset($name) ? $name : '';

    return 'Receive '.$name;
});
$router->get('/hello/{name}?', function ($request) {
    $name = ucwords($request->getAttribute('name', 'World!'));
    // $session = $request->getAttribute('session');
    // $session->set('name', $name);
    $_SESSION['name'] = $name;

    return new Response(200, [], 'hello '.$name);
})->setHost('example.com');

$router->get('/ab123', 'Controller\Index::index');
$services = [
    'factory' => Ypf\Application\Swoole::class,

    'swoole' => [
        'server' => [
            'address' => '*',
            'port' => 8080,
        ],
        'options' => [
        //     'task_worker_num' => 3,
             'dispatch_mode' => 1,
         ],
    ],
    'middleware' => [
        //new Zend\Expressive\Session\SessionMiddleware(new Zend\Expressive\Session\Ext\PhpSessionPersistence()),
        new Middlewares\PhpSession(),
        new Ypf\Route\Middleware($router),
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

<?php

require './vendor/autoload.php';

use Ypf\Application\Factory\SwooleApplicationFactory;
use Ypf\Interfaces\FactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;

$services = [
    FactoryInterface::class => SwooleApplicationFactory::class,
    'swoole' => [
        'listen' => '*:7000',
        'user' => 'nobody',
        'pid_file' => '/tmp/dash.pid',
        'master_process_name' => 'ycs-master',
        'manager_process_name' => 'ycs-manager',
        'worker_process_name' => 'ycs-worker-%d',
        'task_worker_process_name' => 'ycs-task-worker-%d',
        'options' => [
        ],
    ],
    'routes' => [
        [
            'pattern' => '/',
            'middleware' => [
                Middleware\RewriteMiddleware::class,
                Controller\Index::class,
            ],
            'methods' => ['GET'],
        ], [
            'pattern' => '/greet{/{name}}?',
            'middleware' => [
                Middleware\BenchmarkMiddleware::class,
                Controller\Greeter::class,
            ],
            'methods' => ['POST', 'GET', 'PUT'],
        ], [
            'pattern' => '/text{/{name}}?',
            'middleware' => [
                Controller\Text::class,
            ],
        ],
    ],
    'middleware' => [
    ],
    ResponseInterface::class => GuzzleHttp\Psr7\Response::class,
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
    // monolog
$services[\Psr\Log\LoggerInterface::class] = function () {
    $logger = new Monolog\Logger('test');
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::DEBUG));

    return $logger;
};

$services['view'] = new PhpRenderer('./templates');
//echo \Psr\Http\Message\ResponseInterface::class;
$container = new Ypf\Container($services);

$container->get(FactoryInterface::class)->run();

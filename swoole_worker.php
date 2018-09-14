<?php

require './vendor/autoload.php';

use Ypf\Application\Factory\SwooleWorkerApplicationFactory;
use Ypf\Interfaces\FactoryInterface;

$services = [
    FactoryInterface::class => SwooleWorkerApplicationFactory::class,
    'worker' => [
        'single' => [
            //Worker\SingleTest::class,
        ],
        'cron' => [
            //[Worker\CronTest::class, '* * * * *'],
            [Worker\BrokenTest::class, '3'],
        ],
        'options' => [
            'daemonize' => 0,
            'worker_num' => 1,
        ],
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
    // monolog
$services[\Psr\Log\LoggerInterface::class] = function () {
    $logger = new Monolog\Logger('test');
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::DEBUG));

    return $logger;
};

//echo \Psr\Http\Message\ResponseInterface::class;
$container = new Ypf\Container($services);

$container->get(FactoryInterface::class)->run();

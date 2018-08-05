<?php

require './vendor/autoload.php';

use Ypf\Application\Factory\SwooleApplicationFactory;
use Ypf\Interfaces\FactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use Dflydev\FigCookies\SetCookie;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

$services = [
    FactoryInterface::class => SwooleApplicationFactory::class,
    'swoole' => [
        'listen' => '*:7000',
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
            'headers' => [
                'Server' => true,
            ],
        ], [
            'pattern' => '/text{/{name}}?',
            'middleware' => [
                Controller\Text::class,
            ],
        ], [
            'pattern' => '/hello',
            'class' => Ypf\Router\StaticRoute::class,
            'request_handler' => Controller\Index::class,
        ],
    ],
    'middleware' => [
        new SessionMiddleware(
            new Sha256(),
            'c9UA8QKLSmDEn4DhNeJIad/4JugZd/HvrjyKrS0jOes=', // signature key (important: change this to your own)
            'c9UA8QKLSmDEn4DhNeJIad/4JugZd/HvrjyKrS0jOes=', // verification key (important: change this to your own)
            SetCookie::create('an-cookie-name')
                ->withSecure(false) // false on purpose, unless you have https locally
                ->withHttpOnly(true)
                ->withPath('/'),
            new Parser(),
            1200, // 20 minutes
            new SystemClock()
        ),
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
$container = new Ypf\Container($services);

$container->get(FactoryInterface::class)->run();

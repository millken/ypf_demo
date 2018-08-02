<?php

require './vendor/autoload.php';

use Ypf\Application\Factory\ApplicationFactory;
use Ypf\Interfaces\FactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use Dflydev\FigCookies\SetCookie;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

$sessionMiddleware = new SessionMiddleware(
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
);
$services = [
    FactoryInterface::class => ApplicationFactory::class,
    'routes' => [
        [
            'pattern' => '/',
            'middleware' => [
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
        ],
    ],
    'middleware' => [
        $sessionMiddleware,
    ],
    ResponseInterface::class => GuzzleHttp\Psr7\Response::class,
];

$services['db'] = function () {};
    // monolog
$services[\Psr\Log\LoggerInterface::class] = function () {
    $logger = new Monolog\Logger('test');
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::WARNING));

    return $logger;
};
$services['view'] = new PhpRenderer('./templates');

$container = new Ypf\Container($services);

$container->get(FactoryInterface::class)->run();

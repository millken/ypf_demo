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
        'enableCoroutine' => false,
    ],
    'static-files' => [
        //https://docs.zendframework.com/zend-expressive-swoole/static-resources/
        // Document root; defaults to "getcwd() . '/public'"
        'document-root' => getcwd(),

        // Extension => content-type map.
        // Keys are the extensions to map (minus any leading `.`),
        // values are the MIME type to use when serving them.
        // A default list exists if none is provided.
        'type-map' => [],

        // How often a worker should clear the filesystem stat cache.
        // If not provided, it will never clear it. The value should be
        // an integer indicating the number of seconds between clear
        // operations. 0 or negative values will clear on every request.
        'clearstatcache-interval' => 3600,

        // Which ETag algorithm to use.
        // Must be one of "weak" or "strong"; the default, when none is
        // provided, is "weak".
        'etag-type' => 'weak|strong',

        // gzip options
        'gzip' => [
            // Compression level to use.
            // Should be an integer between 1 and 9; values less than 1
            // disable compression.
            'level' => 4,
        ],

        // Rules governing which server-side caching headers are emitted.
        // Each key must be a valid regular expression, and should match
        // typically only file extensions, but potentially full paths.
        // When a static resource matches, all associated rules will apply.
        'directives' => [
            'regex' => [
                'cache-control' => [
                    // one or more valid Cache-Control directives:
                    // - must-revalidate
                    // - no-cache
                    // - no-store
                    // - no-transform
                    // - public
                    // - private
                    // - max-age=\d+
                ],
                'last-modified' => false, // Emit a Last-Modified header?
                'etag' => true, // Emit an ETag header?
            ],
        ],
    ],
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
            'headers' => [
                'Server' => false,
            ],
        ], [
            'pattern' => '/text{/{name}}?',
            'middleware' => [
                Controller\Text::class,
            ],
        ],
        //  [
        //     'pattern' => '/hello',
        //     'class' => Ypf\Router\StaticRoute::class,
        //     'request_handler' => Controller\Index::class,
        // ],
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
    $logger->pushProcessor(new Monolog\Processor\PsrLogMessageProcessor(null, true));
    $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Monolog\Logger::DEBUG));

    return $logger;
};

$services['view'] = new PhpRenderer('./templates');
$container = new Ypf\Container($services);

$container->get(FactoryInterface::class)->run();

<?php
use Ypf\Reference\ParameterReference AS PR;
use Ypf\Reference\ServiceReference AS SR;

use Monolog\Logger;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

return [
	'root' => __DIR__,
	'config' => [
		'class' => \Ypf\Lib\Config::class,
		'calls' => [
			[
				'method' => 'load',
				'arguments' => [
					__CONF__,
				]
			],
		]
	],
    StreamHandler::class => [
        'class' => StreamHandler::class,
        'arguments' => [
            new PR('logger.file'),
            Logger::DEBUG,
        ],
    ],
    NativeMailHandler::class => [
        'class' => NativeMailerHandler::class,
        'arguments' => [
            new PR('logger.mail.to_address'),
            new PR('logger.mail.subject'),
            new PR('logger.mail.from_address'),
            Logger::ERROR,
        ],
    ],
    'logger' => [
        'class' => Logger::class,
        'arguments' => [ 'channel-name' ],
        'calls' => [
            [
                'method' => 'pushHandler',
                'arguments' => [
                    new SR(StreamHandler::class),
                ]
            ],
            [
                'method' => 'pushHandler',
                'arguments' => [
                    new SR(NativeMailHandler::class),
                ]
            ]
        ]
    ]
];

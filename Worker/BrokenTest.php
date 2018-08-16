<?php

declare(strict_types=1);

namespace Worker;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

class BrokenTest
{
    public function run(ContainerInterface $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        go(function () {
            $pg = new \Swoole\Coroutine\PostgreSQL();
            $conn = $pg->connect('host=172.17.0.3 port=5432 dbname=ip user=apostgres password=admin');
            var_dump($conn);

            $result = $pg->query('select * from "17mon" limit 1');
            if ($result !== false) {
                $arr = $pg->fetchAll($result);
                var_dump($arr);
            }
        });
        $logger->error('broken test cron worker');
    }
}

<?php

declare(strict_types=1);

namespace Worker;

use Ypf\Controller\CronWorker;
use Ypf\Swoole\Tasks\Server;
use Ypf\Swoole\Tasks\Task;

class SingleTest extends CronWorker
{
    public function run(): void
    {
        $this->logger->info('send to task worker');

        $task = new Task(\Worker\TaskTest::class, function ($data) {
            $this->logger->info('task callback : '.$data);
        });
        $YSTS = new Server();
        $YSTS->push($task);
        while (true) {
            $this->logger->info('test single'.getmypid());
            \Swoole\Coroutine::sleep(3);
        }
    }
}

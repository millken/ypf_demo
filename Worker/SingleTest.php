<?php

declare(strict_types=1);

namespace Worker;

use Ypf\Controller\CronWorker;

class SingleTest extends CronWorker
{
    public function run(): void
    {
        $this->logger->info('test single');
        while (true) {
            $this->logger->info('test single');
            \Swoole\Coroutine::sleep(3);
        }
    }
}

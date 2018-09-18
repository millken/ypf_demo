<?php

declare(strict_types=1);

namespace Worker;

use Ypf\Controller\CronWorker;

class CronTest extends CronWorker
{
    public function run(): void
    {
        echo 'test'.PHP_EOL;
    }
}

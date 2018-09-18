<?php

declare(strict_types=1);

namespace Worker;

use Ypf\Controller\CronWorker;

class TaskTest extends CronWorker
{
    public function run()
    {
        return 'task test';
    }
}

<?php

namespace Jue\Swoole\Domain\Services;


use Jue\Swoole\Domain\Events\TestEvent;
use Jue\Swoole\Achieves\Services\AbstractHandleService;


class TestService extends AbstractHandleService
{

    public function handle()
    {
        while (true)
        {
            $userId = rand(0,100);
            client()->fire(new TestEvent($userId, $userId));
        }


    }
}
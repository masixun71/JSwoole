<?php

namespace Jue\Swoole\Domain\Listeners;


use Jue\Swoole\Domain\Events\TestEvent;

class TestListener
{


    public function handle(TestEvent &$event)
    {

        logger()->info('获取到的user_id', $event->getUserId());

        return true;
    }


}
<?php

namespace Jue\Swoole\Domain\Clients;


use Jue\Swoole\Domain\Events\AbstractEvent;

interface IClient
{


    /**
     * @param AbstractEvent $event
     * @param $index  //由index标示应该选择的tasker，具体算法可根据index计算
     *                //tasker has id, index to select tasker with id
     * @return mixed
     */
    public function fire(AbstractEvent $event);
}
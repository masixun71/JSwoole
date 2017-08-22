<?php

namespace Jue\Swoole\Domain\Maps;


use Jue\Swoole\Domain\Services\TestService;

class WorkerMap
{

    const MAP = [
        'test' => [
            'class' => TestService::class,
            'des' => '测试处理程序'
        ]
    ];

}
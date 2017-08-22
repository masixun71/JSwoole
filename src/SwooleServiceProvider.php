<?php

namespace Jue\Swoole;

use Jue\Swoole\Achieves\Loggers\Logger;
use Jue\Swoole\Domain\Clients\IClient;
use Jue\Swoole\Domain\Di\Di;
use Jue\Swoole\Domain\Events\TestEvent;
use Jue\Swoole\Domain\Listeners\TestListener;
use Jue\Swoole\Domain\Maps\WorkerMap;
use Jue\Swoole\Achieves\Clients\Client;
use Jue\Swoole\Achieves\Masters\SwooleMaster;

class SwooleServiceProvider
{


    public function register()
    {
        $this->registerClient();
        $this->registerLogger();
        $this->registerSwoole();
    }

    public function registerClient()
    {
        Di::set('client', new Client());
    }


    public function registerLogger()
    {
        Di::set('logger', Logger::getInstance('/tmp'));
    }

    public function registerSwoole()
    {
        $this->registerEvents();
        $this->registerWorkerMap();
    }


    private function registerEvents()
    {
        SwooleMaster::listen([
            TestEvent::class => TestListener::class
        ]);
    }


    private function registerWorkerMap()
    {
        SwooleMaster::registerWorkerMap(WorkerMap::MAP);
    }

}
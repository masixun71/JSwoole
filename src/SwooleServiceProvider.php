<?php

namespace Jue\Swoole;

use Jue\Swoole\Achieves\Loggers\LoggerManager;
use Jue\Swoole\Domain\Clients\IClient;
use Jue\Swoole\Domain\Events\TestEvent;
use Jue\Swoole\Domain\Listeners\TestListener;
use Jue\Swoole\Domain\Loggers\ILogger;
use Jue\Swoole\Domain\Loggers\ILoggerManagerInterface;
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
        container()->singleton(IClient::class, Client::class);
    }


    public function registerLogger()
    {
        container()->instance(ILoggerManagerInterface::class, LoggerManager::class);
        container()->instance(ILogger::class, container()->make(ILoggerManagerInterface::class)->newLogger('/tmp'));
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
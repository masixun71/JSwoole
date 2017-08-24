<?php

namespace Jue\Swoole;

use Jue\Swoole\Achieves\Configs\Config;
use Jue\Swoole\Domain\Clients\IClient;
use Jue\Swoole\Domain\Events\TestEvent;
use Jue\Swoole\Domain\Listeners\TestListener;
use Jue\Swoole\Domain\Loggers\ILogger;
use Jue\Swoole\Domain\Loggers\ILoggerManagerInterface;
use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Domain\Services\TestService;

class SwooleServiceProvider
{

    public function register()
    {
        $this->registerConfig();
        $this->registerClient();
        $this->registerLogger();
        $this->registerSwoole();
    }

    public function registerConfig()
    {
        $config = new Config();

        SwooleMaster::setConfig($config);
    }



    public function registerClient()
    {
        container()->singleton(IClient::class, SwooleMaster::getConfig()->getClient());
    }


    public function registerLogger()
    {
        container()->bind(ILoggerManagerInterface::class, SwooleMaster::getConfig()->getLoggerManager());

        $loggerManager = container()->make(ILoggerManagerInterface::class);
        container()->instance(ILogger::class, $loggerManager->newLogger(SwooleMaster::getConfig()->getLogDir()));
    }

    public function registerSwoole()
    {
        $this->registerWorkerConsumer();
        $this->registerEvents();
    }

    public function registerWorkerConsumer()
    {
        SwooleMaster::getConfig()->addWorker('test', TestService::class, '测试程序');
    }


    public function registerEvents()
    {
        SwooleMaster::getConfig()->listen(TestEvent::class,TestListener::class);
    }



}
<?php

namespace Jue\Swoole\Achieves\Workers;

use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Domain\Loggers\ILogger;
use Jue\Swoole\Domain\Loggers\ILoggerManagerInterface;
use Jue\Swoole\Language\Language;

class SwooleWorker
{

    public static $id;


    public static function init(\swoole_process $worker)
    {
        gc_enable();

        $id = $worker->id;
        self::$id = $id;

        $workerName = sprintf(SwooleMaster::SWOOLE_WORKER_NAME, SwooleMaster::getTopic(), $id);
        @swoole_set_process_name($workerName);
        logger()->info(Language::getWord(Language::WORKER_START),[
            'taskers' => SwooleMaster::getTaskers(),
            'id' => $id,
            'name' => $workerName,
            'consumer' => $worker->consumer,
        ]);

        try
        {
            self::initialize();

            container()->make(SwooleMaster::getConfig()->getWorkerMap()[$worker->consumer]['class'])->handle();

        }catch (\Exception $e)
        {
            logger()->error(Language::getWord(Language::WORKER_ERROR),$e);
            throw $e;
        }
    }

    /**
     * if you need change,please Overloaded this method and add parent::initialize()
     */
    public static function initialize()
    {
        container()->forgetInstance(ILogger::class);
        container()->instance(ILogger::class, container()->make(ILoggerManagerInterface::class)->newLogger(SwooleMaster::getConfig()->getLogDir(), sprintf(SwooleMaster::getTopic()."-worker#%d", self::$id)));
    }

}
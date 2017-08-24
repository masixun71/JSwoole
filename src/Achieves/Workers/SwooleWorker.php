<?php

namespace Jue\Swoole\Achieves\Workers;

use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Domain\Loggers\ILogger;
use Jue\Swoole\Domain\Loggers\ILoggerManagerInterface;

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
        logger()->info("当前启动了worker进程",[
            'taskers' => SwooleMaster::getTaskers(),
            'id' => $id,
            'name' => $workerName,
            'consumer' => $worker->consumer,
        ]);

        try
        {
            //重置子进程logger位置
            self::initialize();

            container()->make(SwooleMaster::getWorkerMap()[$worker->consumer]['class'])->handle();

        }catch (\Exception $e)
        {
            logger()->error("worker进程出错",$e);
            throw $e;
        }
    }

    /**
     * if you need change,please Overloaded this method and add parent::initialize()
     */
    public function initialize()
    {
        container()->forgetInstance(ILogger::class);
        container()->instance(ILogger::class, container()->make(ILoggerManagerInterface::class)->newLogger('/tmp', sprintf(SwooleMaster::getTopic()."-swoole-worker#%d", self::$id)));
    }

}
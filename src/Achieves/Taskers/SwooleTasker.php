<?php

namespace Jue\Swoole\Achieves\Taskers;


use Jue\Swoole\Domain\Events\AbstractEvent;
use Jue\Swoole\Domain\Loggers\ILogger;
use Jue\Swoole\Domain\Loggers\ILoggerManagerInterface;
use Jue\Swoole\Domain\Messages\Processor;
use Jue\Swoole\Domain\Types\WorkerType;
use Jue\Swoole\Achieves\Channels\Channel;
use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Achieves\Tables\CollectTable;
use Jue\Swoole\Achieves\Tables\MarkTable;
use Jue\Swoole\Achieves\Tables\MemoryTable;
use Jue\Swoole\Language\Language;

class SwooleTasker
{

    public static $id;


    public static function init(\swoole_process $worker)
    {
        gc_enable();

        $id = $worker->id;
        self::$id = $id;

        try {

            self::initialize();

            $taskerName = sprintf(SwooleMaster::SWOOLE_TASKER_NAME, SwooleMaster::getTopic(), $worker->id);
            @swoole_set_process_name($taskerName);
            logger()->info(Language::getWord(Language::TASKER_START), [
                'id' => $worker->id,
                'name' => $taskerName,
                'table' => SwooleMaster::getTable()
            ]);

            $messageQueue = msg_get_queue($worker->msgQueueKey, 0666);
            $msgType = 1;
            while (true) {
                try {
                    msg_receive($messageQueue, 0, $msgType, 1024, $event, true);
                    $message = Processor::toMessage($event);

                    //增加统计信息(add collect)
                    CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_RECIVE_KEY, $message);
                    //清除当前处理event (clean mark event)
                    MarkTable::markTable($message, sprintf(SwooleMaster::TABLE_TASKER_MARK_SET, $worker->id));

                    logger()->info(Language::getWord(Language::TASKER_GET_MESSAGE), [
                        'msg_info' => $event,
                        'worker_info' => $worker,
                        'worker_id' => $worker->id,
                    ]);
                    /** @var AbstractEvent $event */
                    if ($event) {
                        $listener = SwooleMaster::getListener($event->getClassName());
                        $res = $listener->handle($event);

                        //增加统计信息(add collect)
                        CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_FINISH_KEY, $message);
                        //清除当前处理event (clean mark event)
                        MarkTable::clearMarkTable(sprintf(SwooleMaster::TABLE_TASKER_MARK_SET, $worker->id));

                        self::checkTaskDone($res, $worker, $message, $event);
                    } else {
                        logger()->error(Language::getWord(Language::TASKER_MESSAGE_ERROR), [
                            'msg_info' => $message,
                            'worker_info' => $worker,
                            'worker_id' => $worker->id,
                        ]);
                    }

                    $memory = (int)(memory_get_usage(true) / (1024 * 1024));
                    logger()->info(Language::getWord(Language::TASKER_MEMORY_STATUS),[
                        'memory' => $memory . "MB",
                        'worker_id' => $worker->id,
                        ]);
                    MemoryTable::setMemory($memory, sprintf(SwooleMaster::TABLE_TASKER_MEMORY_SET, $worker->id),$worker->id);

                } catch (\Exception $e) {
                    logger()->error(Language::getWord(Language::TASKER_PROCESS_ERROR), $e);

                    logger()->notice(Language::getWord(Language::TASKER_MESSAGE_TO_CHANNEL), [
                        'event' => Processor::toMessage($event),
                        'worker_id' => $worker->id
                    ]);
                    Channel::push($event, $worker->id, WorkerType::TASKER);
                    CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_FAIL_KEY, $message);
                }
            }
        }catch (\Exception $e)
        {
            logger()->error(Language::getWord(Language::TASKER_ERROR), $e);
            throw $e;
        }

        $worker->exit(0);
    }

    /**
     * if you need change,please Overloaded this method and add parent::initialize()
     */
    public static function initialize()
    {
        container()->forgetInstance(ILogger::class);
        container()->instance(ILogger::class, container()->make(ILoggerManagerInterface::class)->newLogger(SwooleMaster::getConfig()->getLogDir(), sprintf(SwooleMaster::getTopic() . sprintf("-tasker#%d", self::$id))));
    }

    private static function checkTaskDone($result, $worker, $message, $event)
    {
        if ($result) {
            CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_SUCCESS_KEY, $message);
            logger()->info(Language::getWord(Language::TASKER_PROCESS_SUCCESS), [
                'msg_info' => $message,
                'worker_info' => $worker,
                'worker_id' => $worker->id,
                'result' => $result
            ]);
        } else {
            CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_FAIL_KEY, $message);
            logger()->error(Language::getWord(Language::TASKER_PROCESS_FAIL), [
                'msg_info' => $message,
                'worker_info' => $worker,
                'worker_id' => $worker->id,
                'result' => $result
            ]);
        }
    }

}
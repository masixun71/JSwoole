<?php

namespace Jue\Swoole\Achieves\Taskers;


use Jue\Swoole\Domain\Events\AbstractEvent;
use Jue\Swoole\Domain\Messages\Processor;
use Jue\Swoole\Domain\Types\WorkerType;
use Jue\Swoole\Achieves\Channels\Channel;
use Jue\Swoole\Achieves\Loggers\Logger;
use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Achieves\Tables\CollectTable;
use Jue\Swoole\Achieves\Tables\MarkTable;
use Jue\Swoole\Achieves\Tables\MemoryTable;

class SwooleTasker
{

    public static function init(\swoole_process $worker)
    {
        gc_enable();

        try {

            $taskerName = sprintf(SwooleMaster::SWOOLE_TASKER_NAME, SwooleMaster::getTopic(), $worker->id);
            @swoole_set_process_name($taskerName);
            logger()->info("当前启动了tasker进程", [
                'id' => $worker->id,
                'name' => $taskerName,
                'table' => SwooleMaster::getTable()
            ]);

            self::reset(sprintf("swoole-tasker#%d", $worker->id));
            $messageQueue = msg_get_queue($worker->msgQueueKey, 0666);
            $msgType = 1;
            while (true) {
                try {
//                    $message = $worker->pop();
                    msg_receive($messageQueue, 0, $msgType, 1024, $event, true);
                    $message = Processor::toMessage($event);

                    //增加统计信息
                    CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_RECIVE_KEY, $message);
                    //标记当前处理event
                    MarkTable::markTable($message, sprintf(SwooleMaster::TABLE_TASKER_MARK_SET, $worker->id));

                    logger()->info('从worker获取到信息,tasker开始工作', [
                        '消息内容' => $event,
                        'worker属性' => $worker,
                        'worker_id' => $worker->id,
                    ]);
                    /** @var AbstractEvent $event */
                    if ($event) {
                        $listener = SwooleMaster::getListener($event->getClassName());
                        $res = $listener->handle($event);

                        //增加统计信息
                        CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_FINISH_KEY, $message);
                        //清除当前处理event
                        MarkTable::clearMarkTable(sprintf(SwooleMaster::TABLE_TASKER_MARK_SET, $worker->id));

                        self::checkTaskDone($res, $worker, $message, $event);
                    } else {
                        logger()->error('tasker获取到的消息无法转化为event', [
                            '消息内容' => $message,
                            'worker属性' => $worker,
                            'worker_id' => $worker->id,
                        ]);
                    }

                    $memory = (int)(memory_get_usage(true) / (1024 * 1024));
                    logger()->info('当前tasker进程内存使用情况',[
                        'memory' => $memory . "MB",
                        'worker_id' => $worker->id,
                        ]);
                    MemoryTable::setMemory($memory, sprintf(SwooleMaster::TABLE_TASKER_MEMORY_SET, $worker->id),$worker->id);

                } catch (\Exception $e) {
                    logger()->error("tasker进程处理过程出现异常,记录该异常", $e);

                    logger()->notice('tasker处理event失败,发送进channel等待重复处理', [
                        'event' => Processor::toMessage($event),
                        'worker_id' => $worker->id
                    ]);
                    Channel::push($event, $worker->id, WorkerType::TASKER);
                    CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_FAIL_KEY, $message);
                }
            }
        }catch (\Exception $e)
        {
            logger()->error("tasker进程出现异常", $e);
            throw $e;
        }

        $worker->exit(0);
    }

    private static function reset($taskerName)
    {
        //重置子进程日志位置 todo
        Di::set('logger', Logger::getInstance('/tmp', sprintf(SwooleMaster::getTopic() . '-' . $taskerName)));
        //重置数据库链接 todo
    }


    private static function checkTaskDone($result, $worker, $message, $event)
    {
        if ($result) {
            CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_SUCCESS_KEY, $message);
            logger()->info('tasker处理消息成功', [
                '消息内容' => $message,
                'worker属性' => $worker,
                'worker_id' => $worker->id,
                '执行结果' => $result
            ]);
        } else {
            CollectTable::incrTable(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $worker->id), SwooleMaster::TABLE_FAIL_KEY, $message);
            logger()->error('tasker处理消息失败', [
                '消息内容' => $message,
                'worker属性' => $worker,
                'worker_id' => $worker->id,
                '执行结果' => $result
            ]);
        }
    }

}
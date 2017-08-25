<?php

namespace Jue\Swoole\Achieves\Managers;


use Jue\Swoole\Domain\Clients\IClient;
use Jue\Swoole\Domain\Events\AbstractEvent;
use Jue\Swoole\Domain\Loggers\ILogger;
use Jue\Swoole\Domain\Loggers\ILoggerManagerInterface;
use Jue\Swoole\Domain\Messages\Processor;
use Jue\Swoole\Domain\Types\WorkerType;
use Jue\Swoole\Achieves\Channels\Channel;
use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Language\Language;

class SwooleManager
{

    private $workerNum;
    private $taskerNum;
    private $taskers = [];
    private $workers = [];

    private $redirect_stout = false;
    private $restart = false;

    /**
     * @return int
     */
    public function getWorkerNum()
    {
        return $this->workerNum;
    }

    /**
     * @return int
     */
    public function getTaskerNum()
    {
        return $this->taskerNum;
    }

    public function getTaskers()
    {
        return $this->taskers;
    }

    public function getWorkers()
    {
        return $this->workers;
    }

    /**
     * @return bool
     */
    public function isRedirectStout()
    {
        return $this->redirect_stout;
    }

    public function __construct($workerNum = 1, $taskerNum)
    {
        $this->workerNum = $workerNum;
        $this->taskerNum = $taskerNum;
    }


    private function checkParams($consumerName, $taskerNum, $msgStart, $msgEnd)
    {
        if(!is_null(SwooleMaster::getConfig()->getWorkerMap()[$consumerName]))
        {
            if ($msgStart <= 0)
            {
                throw new \Exception('msgStart 必须大于0[msgStart must more than 0]');
            }

            if (($msgEnd - $msgStart) != $taskerNum)
            {
                throw new \Exception('消息队列使用的是[start, end-1],务必范围总数和启动的tasker数量保持一致[msgKey in [start, end-1], you need set (taskerNum == msgEnd - msgStart)]');
            }
        }
        else
        {
            throw new \Exception('不存在该consumer[The consumer does not exist]');
        }
    }



    public function start($consumerName, $taskerNum, $msgStart, $msgEnd)
    {
        gc_enable();

        $this->checkParams($consumerName, $taskerNum, $msgStart, $msgEnd);

        $this->init($consumerName, $msgStart);
        $this->doWorker();
    }


    private function init($consumerName, $msgStart)
    {
        SwooleMaster::setTopic($consumerName);
        $this->initLog();
        $this->clear();
        $this->initCollectTable();
        $this->initMemoryTable();
        $this->initTaskerTable();
        $this->initTaskerCount();
        $this->initMarkTable();
        $this->initChannel();
        $this->initTaskerProcess($msgStart);
        $this->resetTaskerTable();

        $this->initWorkerProcess($consumerName);
        $this->initSignal();
    }


    private function clear()
    {
        $cmd = "ps -ef | grep swoole-" . SwooleMaster::getTopic() . " | awk '{print $2}' | xargs kill -9";
        logger()->info(Language::getWord(Language::CLEAN_MANAGER), [
            'cmd' => $cmd
        ]);
        shell_exec($cmd);
    }


    private function initLog()
    {
        container()->forgetInstance(ILogger::class);
        container()->instance(ILogger::class, container()->make(ILoggerManagerInterface::class)->newLogger(SwooleMaster::getConfig()->getLogDir(), SwooleMaster::getTopic()));
    }


    private function initTaskerProcess($msgStart)
    {
        logger()->info('初始化swoole-manager-taskers', [
            '数量' => $this->getTaskerNum()
        ]);
        for ($i = 0; $i < $this->getTaskerNum(); $i++) {
            $tasker = new \swoole_process([SwooleMaster::getConfig()->getTasker(), 'init'], $this->isRedirectStout(), false);
            $tasker->useQueue($i + $msgStart);
            $tasker->id = $i;
            $tasker->start();
            $this->taskers[] = $tasker;
        }

        SwooleMaster::setTaskers($this->taskers);

        logger()->info('初始化swoole-manager-taskers完成', $this->taskers);
    }


    private function initWorkerProcess($consumer)
    {
        logger()->info('初始化swoole-manager-worker', [
            '数量' => $this->getWorkerNum()
        ]);
        for ($i = 0; $i < $this->getWorkerNum(); $i++) {
            $worker = new \swoole_process([SwooleMaster::getConfig()->getWorker(), 'init'], $this->isRedirectStout(), false);
            $worker->id = $i;
            $worker->consumer = $consumer;
            $worker->start();

            $this->workers[] = $worker;
        }
        logger()->info('初始化swoole-manager-worker完成', $this->workers);
    }


    private function initSignal()
    {
        \swoole_process::signal(SIGCHLD, [$this, 'sigchldHandle']);
        \swoole_process::signal(SIGTERM, [$this, 'sigtermHandle']);
    }


    public function sigtermHandle()
    {
        logger()->info('swoole总进程正在正常退出');

        $allProcess = [];

        logger()->info('swoole开始退出worker进程');
        //退出worker进程
        foreach ($this->workers as $id => $processObj) {
            \swoole_process::kill($processObj->pid, SIGKILL);

            $allProcess[$processObj->pid] = $processObj;
        }
        logger()->info('swoole检测到所有worker进程退出完成');
        $table = SwooleMaster::getTable();

        logger()->info('swoole开始退出tasker进程');
        $allTaskers = $this->taskers;


        $startTime = time();

        while (true) {
            //检测系统中所有任务完成并退出tasker进程
            foreach ($allTaskers as $id => $processObj) {
                $taskerTableCollectStatus = $table->get(sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $id));
                $receiveCount = $taskerTableCollectStatus[SwooleMaster::TABLE_RECIVE_KEY];
                $finishCount = $taskerTableCollectStatus[SwooleMaster::TABLE_SUCCESS_KEY] + $taskerTableCollectStatus[SwooleMaster::TABLE_FAIL_KEY];

                if (($receiveCount - $finishCount) == 0) {
                    $msgQueueId = $processObj->msgQueueId;
                    \swoole_process::kill($processObj->pid, SIGKILL);

                    if (!SwooleMaster::getConfig()->isRestartByNotLoseMessage())
                    {
                        //清除对应的tasker消息队列，必须在杀死tasker后清除
                        shell_exec("ipcrm -q {$msgQueueId} | sh > /dev/null 2>&1;");
                    }
                    $allProcess[$processObj->pid] = $processObj;
                    unset($allTaskers[$id]);
                }
            }

            if (empty($allTaskers)) {
                logger()->info('swoole检测到所有tasker进程退出完成');
                break;
            }

            $endTime = time();
            if ($endTime > ($startTime + SwooleMaster::getConfig()->getTaskerWorkingForWaitSecondByStop())) {
                //退出tasker进程
                foreach ($this->taskers as $id => $processObj) {
                    \swoole_process::kill($processObj->pid, SIGKILL);
                    $allProcess[$processObj->pid] = $processObj;
                    if (!SwooleMaster::getConfig()->isRestartByNotLoseMessage())
                    {
                        $msgQueueId = $processObj->msgQueueId;
                        //清除对应的tasker消息队列，必须在杀死tasker后清除
                        shell_exec("ipcrm -q {$msgQueueId} | sh > /dev/null 2>&1;");
                    }
                }
                break;
            }
        }

        //等待捕获所有子进程退出
        while (!empty($allProcess)) {
            while ($result = \swoole_process::wait(false)) {
                $pid = $result['pid'];
                unset($allProcess[$pid]);
            }
        }
        logger()->info('swoole检测到所有子进程退出完成');

        //删除系统的msg_queue
//        shell_exec('ipcs -q | awk \'{ print "ipcrm -q "$2}\' | sh > /dev/null 2>&1;');

        //主进程退出
        swoole_event_exit();
        logger()->info('swoole所有进程退出完成');
    }


    public function sigchldHandle()
    {
        while ($ret = \swoole_process::wait(false)) {
            logger()->error('有子进程异常退出', $ret);

            foreach ($this->workers as $id => $processObj) {
                /** @var \swoole_process $processObj */
                if ($processObj->pid == $ret['pid']) {
                    logger()->error('异常退出的进程是worker进程', $ret);
                    $isSuccess = $processObj->start();
                    $this->checkProcessError($isSuccess, $ret, $id);

                    $this->workers[$id] = $processObj;
                    break;
                }
            }

            foreach ($this->taskers as $id => $processObj) {
                if ($processObj->pid == $ret['pid']) {

                    logger()->error('异常退出的进程是tasker进程', $ret);

                    $newTasker = new \swoole_process([SwooleMaster::getConfig()->getTasker(), 'init'], $this->isRedirectStout(), false);
                    $newTasker->useQueue($processObj->msgQueueKey);
                    $newTasker->id = $processObj->id;
                    $isSuccess = $newTasker->start();

                    logger()->info('新重启的进程信息', $newTasker);
                    $this->checkProcessError($isSuccess, $ret, $id);
                    unset($this->taskers[$id]);
                    $this->taskers[$id] = $newTasker;
                    //检查是否存在未处理完的消息
                    $this->startMarkEvent($id);

                    break;
                }
            }
        }
    }


    private function startMarkEvent($id)
    {
        $table = SwooleMaster::getMarkTable();
        $arr = $table->get(sprintf(SwooleMaster::TABLE_TASKER_MARK_SET, $id));

        if ($arr) {
            $message = $arr[SwooleMaster::TABLE_MARK_KEY];
            if (!empty($message)) {
                $messageVO = Processor::getMessage($message);
                if ($event = $messageVO->buildEvent())
                {
                    Channel::push($messageVO->buildEvent(), $id, WorkerType::MANAGER);
                    logger()->info('发现tasker进程异常退出时存在未完成的event,放入重试队列', [
                        'message' => $message,
                        'worker_id' => $id
                    ]);
                }
            }
        }
    }


    public function checkProcessError($isSuccess, $process, $id)
    {
        if ($isSuccess) {
            logger()->info('异常退出的进程重启成功', [
                '进程' => $process,
                'id' => $id
            ]);
        } else {
            logger()->info('异常退出的进程重启失败', [
                '进程' => $process,
                'id' => $id
            ]);
        }
    }


    private function initMemoryTable()
    {
        $table = new \swoole_table(2 << 8);

        $table->column(SwooleMaster::TABLE_MEMORY_KEY, \swoole_table::TYPE_INT);
        $table->column(SwooleMaster::TABLE_ID_KEY, \swoole_table::TYPE_INT);
        $res = $table->create();
        if ($res) {
            logger()->info("创建内存统计表成功");
        } else {
            throw new \Exception("创建内存统计表失败");
        }

        SwooleMaster::setMemoryTable($table);
    }

    private function initTaskerTable()
    {
        $table = new \swoole_table(2 << 8);

        $table->column(SwooleMaster::TABLE_MSG_KEY, \swoole_table::TYPE_INT);
        $res = $table->create();
        if ($res) {
            logger()->info("创建tasker进程表成功");
        } else {
            throw new \Exception("创建tasker进程表失败");
        }

        SwooleMaster::setTaskerTable($table);
    }

    private function resetTaskerTable()
    {
        $table = SwooleMaster::getTaskerTable();
        foreach ($this->taskers as $tasker)
        {
            $table->set($tasker->id, [
                SwooleMaster::TABLE_MSG_KEY => $tasker->msgQueueKey
            ]);
            logger()->info('tasker_table',$table->get($tasker->id));
        }

    }


    private function initTaskerCount()
    {
        $taskerCount = new \swoole_atomic($this->taskerNum);
        SwooleMaster::setTaskerCount($taskerCount);
    }



    private function initCollectTable()
    {
        $table = new \swoole_table(2 << 8);

        $table->column(SwooleMaster::TABLE_RECIVE_KEY, \swoole_table::TYPE_INT);
        $table->column(SwooleMaster::TABLE_FINISH_KEY, \swoole_table::TYPE_INT);
        $table->column(SwooleMaster::TABLE_SUCCESS_KEY, \swoole_table::TYPE_INT);
        $table->column(SwooleMaster::TABLE_FAIL_KEY, \swoole_table::TYPE_INT);
        $res = $table->create();
        if ($res) {
            logger()->info("创建通信统计表成功");
        } else {
            throw new \Exception("创建通信统计表失败");
        }

        SwooleMaster::setTable($table);
    }

    private function initMarkTable()
    {
        $table = new \swoole_table(2 << 8);

        $table->column(SwooleMaster::TABLE_MARK_KEY, \swoole_table::TYPE_STRING, 2 << 10);
        $res = $table->create();

        if ($res) {
            logger()->info("创建标记内存表成功");
        } else {
            logger()->info("创建标记内存表失败");
        }

        SwooleMaster::setMarkTable($table);
    }


    private function initChannel()
    {
        $channel = new \Swoole\Channel(SwooleMaster::getConfig()->getChannelSize());

        SwooleMaster::setChannel($channel);
    }


    private function doWorker()
    {
        @swoole_set_process_name(sprintf(SwooleMaster::SWOOLE_MANAGER_NAME, SwooleMaster::getTopic()));

        $table = SwooleMaster::getTable();

        //处理channel
        swoole_timer_tick(SwooleMaster::getConfig()->getTimerTickForChannel(), function ($timeId, $params) {

            if (count($this->taskers) > 0)
            {
                /** @var IClient $client */
                $client = container()->make(IClient::class);
                /** @var AbstractEvent $event */
                $count = $params['channel']->stats()['queue_num'];
                for ($i = 0; $i < $count; $i++) {
                    $event = $params['channel']->pop();
                    if ($event) {
                        logger()->info("获取到event,发送给tasker", ['message' => Processor::toMessage($event)]);
                        $client->fire($event);
                    }
                }
            }
        }, [
            'channel' => SwooleMaster::getChannel(),
        ]);


        //定点输出tasker处理状态
        swoole_timer_tick(SwooleMaster::getConfig()->getTimerTckForTaskerWorkStatus(), function ($timeId, $params) {
            foreach ($params['tasker'] as $tasker) {
                $key = sprintf(SwooleMaster::TABLE_TASKER_COLLECT_COUNT, $tasker->id);
                if ($params['table']->get($key)) {
                    logger()->info("当前进程运行状态:{$key}", $params['table']->get($key));
                }
            }
        }, [
            'table' => $table,
            'tasker' => $this->taskers
        ]);


        //定点检查子进程内存状态
        swoole_timer_tick(SwooleMaster::getConfig()->getTimerTckForTaskerMemoryStatus(), function ($timeId, $params) {
            $table = SwooleMaster::getMemoryTable();

            $limit = 100;
            foreach ($this->taskers as $tasker) {
                $key = sprintf(SwooleMaster::TABLE_TASKER_MEMORY_SET, $tasker->id);
                if ($data = $table->get($key)) {
                    logger()->info("当前tasker进程内存状态", $data);

                    if ($data[SwooleMaster::TABLE_MEMORY_KEY] >= $limit) {
                        logger()->info("当前tasker进程内存已超过限制值{$limit}MB，自杀重启", $data);
                        \swoole_process::kill($tasker->pid, SIGKILL);
                        $table->set($key, [
                            SwooleMaster::TABLE_MEMORY_KEY => 0,
                            SwooleMaster::TABLE_ID_KEY => $data['id'],
                        ]);
                    }

                }
            }
        }, [
        ]);



        //定点输出内存使用情况
        swoole_timer_tick(SwooleMaster::getConfig()->getTimerTckForManagerMemoryStatus(), function ($timeId, $params) {

            if ($this->restart) {
                logger()->info("重启当前manager进程", [
                    'pid' => posix_getpid()
                ]);
                \swoole_process::kill(posix_getpid(), SIGTERM);
            }

            $memory = (int)(memory_get_usage(true) / (1024 * 1024));
            logger()->info('当前manager进程内存使用情况', [
                'memory' => $memory . "MB",
            ]);

            $limit = SwooleMaster::getConfig()->getMaxManagerMemory();
            if ($memory >= $limit) {
                logger()->info("当前manager进程内存已超过限制值{$limit}MB，需要进行重启");
                $this->restart = true;

                //先杀worker进程，保持没有新任务进来
                foreach ($this->workers as $id => $processObj) {
                    \swoole_process::kill($processObj->pid, SIGKILL);
                    $allProcess[$processObj->pid] = $processObj;
                }

            }

        }, []);

    }
}
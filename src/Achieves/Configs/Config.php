<?php

namespace Jue\Swoole\Achieves\Configs;

use Jue\Swoole\Achieves\Clients\Client;
use Jue\Swoole\Achieves\Loggers\LoggerManager;
use Jue\Swoole\Achieves\Taskers\SwooleTasker;
use Jue\Swoole\Achieves\Workers\SwooleWorker;

class Config
{
    /**
     * logDir
     *
     * @var string
     */
    private $logDir = '/tmp';


    private $worker = SwooleWorker::class;


    private $tasker = SwooleTasker::class;


    /**
     *
     * 是否选择重启不丢失数据模式（采用了linux消息队列，不清除队列）
     * when stop manager,this mode is not kill linux-Message
     * @var bool
     */
    private $restartByNotLoseMessage = true;

    /**
     *
     * 当停止JSwoole进程时，最长等待tasker进程处理时间(秒)
     * when stop manager, wait for tasker process second
     * @var int
     */
    private $taskerWorkingForWaitSecondByStop = 60;

    /**
     * 管理进程和tasker相互传递数据的管道大小
     * manager and tasker send msg max size
     * @var int
     */
    private $channelSize = 1024 * 128;


    /**
     *
     * 处理channel数据的间隔(ms)
     * every tick, work for channel(ms)
     * @var int
     */
    private $timerTickForChannel = 500;


    /**
     * 输出tasker处理消息的状态间隔(ms)
     * every tick, print tasker's workStatus(ms)
     * @var int
     */
    private $timerTckForTaskerWorkStatus = 1000 * 9;


    /**
     * 输出tasker的内存状态间隔(ms)
     * every tick, print tasker's memoryStatus(ms)
     * @var int
     */
    private $timerTckForTaskerMemoryStatus = 1000 * 60;


    /**
     * 输出manager的内存状态间隔(ms)
     * every tick, print manager's memoryStatus(ms)
     * @var int
     */
    private $timerTckForManagerMemoryStatus = 1000 * 70;


    /**
     * manager进程最大内存值(MB)，超过后，再下一次检测manager时会自杀(SIGTERM)，最好配合supervisod重启
     * max manager memory(MB), more than, next tick, will kill myself(SIGTERM), you need supervisod
     * @var int
     */
    private $maxManagerMemory = 60;


    /**
     *
     * 先把业务处理程序放入workerMap, 你才可以利用Swoole启动
     * First put the business process into the workerMap, you can use Swoole to start
     * @var array
     */
    private $workerMap = [];

    /**
     * 添加特定业务对应的event和listener,可参考TestEvent,TestListener
     * Add event and listener for a specific service, see TestEvent, TestListener
     * @var
     */
    private $listeners;

    /**
     * worker使用的发送消息客户端class
     * The worker uses the Send Message client class
     * @var string
     */
    private $client = Client::class;


    /**
     * 生成logger的管理工具 class
     * Generate logger management tool class
     * @var string
     */
    private $loggerManager = LoggerManager::class;

    /**
     * @return string
     */
    public function getLoggerManager()
    {
        return $this->loggerManager;
    }

    /**
     * @param string $loggerManager
     * @return $this
     */
    public function setLoggerManager($loggerManager)
    {
        $this->loggerManager = $loggerManager;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param mixed $listeners
     * @return $this
     */
    public function listen($eventClass, $listenerClass)
    {
        if (!isset($this->listeners[$eventClass]))
        {
            $this->listeners[$eventClass] = $listenerClass;
        }

        return $this;
    }


    /**
     * @return array
     */
    public function getWorkerMap()
    {
        return $this->workerMap;
    }

    /**
     * @param array $workerMap
     * @return $this
     */
    public function addWorker($alias, $class, $des)
    {
        if (!isset($this->workerMap[$alias]))
        {
            $this->workerMap[$alias] = [
                    'class' => $class,
                    'des' => $des
            ];
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getTimerTckForTaskerWorkStatus()
    {
        return $this->timerTckForTaskerWorkStatus;
    }

    /**
     * @param int $timerTckForTaskerWorkStatus
     * @return $this
     */
    public function setTimerTckForTaskerWorkStatus($timerTckForTaskerWorkStatus)
    {
        $this->timerTckForTaskerWorkStatus = $timerTckForTaskerWorkStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimerTckForTaskerMemoryStatus()
    {
        return $this->timerTckForTaskerMemoryStatus;
    }

    /**
     * @param int $timerTckForTaskerMemoryStatus
     * @return $this
     */
    public function setTimerTckForTaskerMemoryStatus($timerTckForTaskerMemoryStatus)
    {
        $this->timerTckForTaskerMemoryStatus = $timerTckForTaskerMemoryStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimerTckForManagerMemoryStatus()
    {
        return $this->timerTckForManagerMemoryStatus;
    }

    /**
     * @param int $timerTckForManagerMemoryStatus
     * @return $this
     */
    public function setTimerTckForManagerMemoryStatus($timerTckForManagerMemoryStatus)
    {
        $this->timerTckForManagerMemoryStatus = $timerTckForManagerMemoryStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxManagerMemory()
    {
        return $this->maxManagerMemory;
    }

    /**
     * @param int $maxManagerMemory
     * @return $this
     */
    public function setMaxManagerMemory($maxManagerMemory)
    {
        $this->maxManagerMemory = $maxManagerMemory;
        return $this;
    }


    /**
     * @return int
     */
    public function getTimerTickForChannel()
    {
        return $this->timerTickForChannel;
    }

    /**
     * @param int $timerTickForChannel
     * @return $this
     */
    public function setTimerTickForChannel($timerTickForChannel)
    {
        $this->timerTickForChannel = $timerTickForChannel;
        return $this;
    }



    /**
     * @return int
     */
    public function getChannelSize()
    {
        return $this->channelSize;
    }

    /**
     * @param int $channelSize
     * @return $this
     */
    public function setChannelSize($channelSize)
    {
        $this->channelSize = $channelSize;
        return $this;
    }



    /**
     * @return string
     */
    public function getLogDir()
    {
        return $this->logDir;
    }

    /**
     * @param string $logDir
     * @return $this
     */
    public function setLogDir($logDir)
    {
        $this->logDir = $logDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param string $worker
     * @return $this
     */
    public function setWorker($worker)
    {
        $this->worker = $worker;
        return $this;
    }

    /**
     * @return string
     */
    public function getTasker()
    {
        return $this->tasker;
    }

    /**
     * @param string $tasker
     * @return $this
     */
    public function setTasker($tasker)
    {
        $this->tasker = $tasker;
        return $this;
    }


    /**
     * @return int
     */
    public function getTaskerWorkingForWaitSecondByStop()
    {
        return $this->taskerWorkingForWaitSecondByStop;
    }

    /**
     * @param int $taskerWorkingForWaitSecondByStop
     * @return $this
     */
    public function setTaskerWorkingForWaitSecondByStop($taskerWorkingForWaitSecondByStop)
    {
        $this->taskerWorkingForWaitSecondByStop = $taskerWorkingForWaitSecondByStop;
        return $this;
    }




    /**
     * @return bool
     */
    public function isRestartByNotLoseMessage()
    {
        return $this->restartByNotLoseMessage;
    }

    /**
     * @param bool $restartByNotLoseMessage
     * @return $this
     */
    public function setRestartByNotLoseMessage($restartByNotLoseMessage)
    {
        $this->restartByNotLoseMessage = $restartByNotLoseMessage;
        return $this;
    }







}
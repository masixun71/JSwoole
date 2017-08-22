<?php

namespace Jue\Swoole\Achieves\Managers;


use Jue\Swoole\Domain\Events\AbstractEvent;
use Jue\Swoole\Domain\Messages\Processor;
use Swoole\Server;

class TestSwooleManager
{



    public function onStart(Server $server)
    {
        echo "swooleManager 启动\n";

    }

    public function onWorkerStart()
    {
        $this->init();
    }


    public function onConnect(Server $server, $clientFd, $fromId)
    {
        echo "客户端:{$clientFd}进行连接\n";

        $server->send($clientFd, "与swooleManager 取得连接");
    }

    public function onReceive(Server $server, $clientFd, $fromId, $data)
    {
        echo "从客户端{$clientFd}获取消息:\n";

        $messageDataVO = Processor::getMessage($data);
        $messageDataVO->addListener($this->listeners[$messageDataVO->getEventName()]);

        $server->task($messageDataVO->encode());
    }

    public function onClose(Server $server, $clientFd, $fromId)
    {
        echo "客户端{$clientFd}关闭了连接\n";
    }

    public function onTask(Server $server, $taskId, $fromId, $data)
    {
        $messageDataVO = Processor::getMessage($data);
        /** @var AbstractEvent $event */
        $event = $messageDataVO->buildEvent();
        $listenter = $messageDataVO->buildListener();

        $listenter->handle($event);
    }

    public function onFinish(Server $server, $taskId, $data)
    {
        echo "Task:{$taskId}已结束\n";
    }



}
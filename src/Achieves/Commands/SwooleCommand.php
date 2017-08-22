<?php

namespace Jue\Swoole\Achieves\Commands;


use Jue\Swoole\Domain\Maps\WorkerMap;
use Jue\Swoole\Achieves\Managers\SwooleManager;
use Jue\Swoole\Achieves\Masters\SwooleMaster;

class SwooleCommand
{
    /**
     * @var string
     */
    protected $signature = 'swoole 
                            { do? : 执行命令 }
                            { consumer? : 启动的consumer }
                            { tasker_num? : 启动的tasker数量 }
                            { msg_start? : 启用消息队列key的起始值 }
                            { msg_end? : 启用消息队列key的结束值 }
                            ';
    //消息队列使用的是[start, end-1],务必范围总数和启动的tasker数量保持一致,
    //若系统中消息队列已经存在，会报错，请使用还未创建的消息队列的key

    protected $description = 'swoole管理命令';

    /**
     * 处理函数.
     *
     * @return mixed
     */
    public function handle()
    {
        $do = $this->argument('do');
        $consumer = $this->argument('consumer');
        $taskerNum = $this->argument('tasker_num');
        $msgStart = $this->argument('msg_start');
        $msgEnd = $this->argument('msg_end');

        switch ($do)
        {
            case 'start':
                $this->start($consumer, $taskerNum, $msgStart, $msgEnd);
                break;
            case 'show':
                $this->showTable();
                break;
            case 'help':
            default:
                $this->showHelp();
                break;
        }
    }



    private function start($consumer, $taskerNum, $msgStart, $msgEnd)
    {
        if (empty($consumer) || empty($taskerNum) || empty($msgStart) || empty($msgEnd))
        {
            throw new LogicException('缺少参数，请检查swoole，start参数');
        }


        if(!is_null(SwooleMaster::getWorkerMap()[$consumer]))
        {
            $this->checkMsg($taskerNum, $msgStart, $msgEnd);

            $swooleManager = new SwooleManager(1, $taskerNum);
            $swooleManager->start($consumer, $msgStart);
        }
        else
        {
            throw new LogicException('不存在该consumer');
        }
    }


    private function checkMsg($taskerNum, $msgStart, $msgEnd)
    {
        if ($msgStart <= 0)
        {
            throw new LogicException('msgStart 必须大于0');
        }

        if (($msgEnd - $msgStart) != $taskerNum)
        {
            throw new LogicException('消息队列使用的是[start, end-1],务必范围总数和启动的tasker数量保持一致');
        }

//        for ($i = $msgStart; $i < $msgEnd; $i++)
//        {
//            $str = sprintf("%08X", $i);
//            $cmd = "ipcs -q | awk '{print $1}' | grep {$str}";
//            $res = shell_exec($cmd);
//            if (!empty($res))
//            {
//                throw new LogicException("有存在的消息队列key:{$i},请使用不存咋的key");
//            }
//        }

    }



    private function showTable()
    {
        $this->getOutput()->writeln("以下列表为可用的命令简写对应的处理");
        $this->getOutput()->writeln("使用例子(php swoole start class-course-schedule-for-order 2 1 3)");
        $this->getOutput()->writeln("调用的命令\t\t处理类\t\t描述");
        foreach (WorkerMap::MAP as $key => $item)
        {
            $this->getOutput()->writeln("{$key}\t{$item['class']}\t{$item['des']}");
        }
    }

    private function showHelp()
    {
        $this->getOutput()->writeln("若您想启用swoole多进程业务处理程序，请关注以下内容");
        $this->getOutput()->writeln("目前提供3个命令使用,start show help");
        $this->getOutput()->writeln("请将你的业务worker程序放入到WokerMap中");
        $this->getOutput()->writeln("注意（业务worker务必继承AbstractHandleService）");
        $this->getOutput()->writeln("使用show命令查看如何使用");
        $this->getOutput()->writeln("start 命令后面需要四个参数: start consumer tasker_num msg_start msg_end");
        $this->getOutput()->writeln("start consumer: 你在WorkerMap中定义的consumerKey");
        $this->getOutput()->writeln("start tasker_num: tasker进程数量");
        $this->getOutput()->writeln("start msg_start: 启用消息队列key的起始值");
        $this->getOutput()->writeln("start msg_start: 启用消息队列key的结束值");
        $this->getOutput()->writeln("消息队列使用的是[start, end-1],务必范围总数和启动的tasker数量保持一致");
        $this->getOutput()->writeln("若系统中消息队列已经存在，会报错，请使用还未创建的消息队列的key");
        $this->getOutput()->writeln("建议配合supervisord 使用");
    }

}
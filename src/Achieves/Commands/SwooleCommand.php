<?php

namespace Jue\Swoole\Achieves\Commands;


use Jue\Swoole\Achieves\Managers\SwooleManager;
use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\SwooleServiceProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SwooleCommand extends Command
{
    //消息队列使用的是[start, end-1],务必范围总数和启动的tasker数量保持一致,
    //若系统中消息队列已经存在，会报错，请使用还未创建的消息队列的key

    /** @var InputInterface $input */
    private $input;
    /** @var OutputInterface $output */
    private $output;


    protected function configure()
    {
        $this->setName('swoole')
            ->setDescription('swoole-management-tools')
            ->setHelp('This command allows you to create swoole-manager');

        $this->addArgument('do', InputArgument::OPTIONAL)
            ->addArgument('consumer', InputArgument::OPTIONAL)
            ->addArgument('tasker_num', InputArgument::OPTIONAL)
            ->addArgument('msg_start', InputArgument::OPTIONAL)
            ->addArgument('msg_end', InputArgument::OPTIONAL);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $do = $this->input->getArgument('do');
        $consumerName = $this->input->getArgument('consumer');
        $taskerNum = $this->input->getArgument('tasker_num');
        $msgStart = $this->input->getArgument('msg_start');
        $msgEnd = $this->input->getArgument('msg_end');

        (new SwooleServiceProvider())->register();

        switch ($do) {
            case 'start':
                $this->start($consumerName, $taskerNum, $msgStart, $msgEnd);
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


    private function start($consumerName, $taskerNum, $msgStart, $msgEnd)
    {
        if (empty($consumerName) || empty($taskerNum) || empty($msgStart) || empty($msgEnd)) {
            throw new LogicException('缺少参数，请检查swoole，start参数[Missing parameters, please check swoole, start parameter]');
        }

        $swooleManager = new SwooleManager(1, $taskerNum);
        $swooleManager->start($consumerName, $taskerNum, $msgStart, $msgEnd);
    }

    private function showTable()
    {
        $io = new SymfonyStyle($this->input, $this->output);
        $io->title('table展示可执行的consumer程序<info>[table display can consumer process]</info>');
        $io->section('使用例子<info>[example]</info>(php console swoole start test 2 1 3)');

        $map = [];
        foreach (SwooleMaster::getConfig()->getWorkerMap() as $key => $item) {
            $map[] = [$key, $item['class'], $item['des']];
        }
        $io->table(
            array('调用的命令[command]', '执行类[class]', '描述[desc]'),
            $map
        );
        $io->newLine(1);
    }

    private function showHelp()
    {
        $io = new SymfonyStyle($this->input, $this->output);

        $io->title("<info>JSwoole基本使用帮助[JSwoole help]</info>");
        $io->section("推荐配合supervisord 使用");
        $io->section("目前提供3个命令使用,start, show, help");
        $io->section('help');
        $io->listing(array(
            '查看基本使用帮助<info>[basic help]</info>',
        ));
        $io->section('show');
        $io->listing(array(
            'table展示可执行的consumer程序[table display can consumer process]',
        ));
        $io->section('start');
        $io->writeln("start 命令后面需要四个参数<info>[The start command requires four arguments]</info>: start <comment>consumer</comment> <comment>tasker_num</comment> <comment>msg_start</comment> <comment>msg_end</comment>");
        $io->listing(array(
            '<comment>consumer</comment>: 你在WorkerMap中定义的consumerKey<info>[start consumer name]</info>',
            '<comment>tasker_num</comment>: tasker进程数量<info>[start tasker amount]</info>',
            '<comment>msg_start</comment>: 启用消息队列key的起始值<info>[linux msgKey start]</info>',
            '<comment>msg_end</comment>: 启用消息队列key的结束值<info>[linux msgKey end]</info>'
        ));
        $io->warning('消息队列使用的是[start, end-1],务必范围总数和启动的tasker数量保持一致<info>[msgKey in [start, end-1], you need set (taskerNum == msgEnd - msgStart)]</info>');

        $io->newLine(2);
        $io->section('怎么添加业务程序?<info>[How to add my process?]</info>');
        $io->text('查看SwooleServiceProvider<info>[ Look at SwooleServiceProvider]</info>');
        $io->text('分为[has]<comment>registerEvents</comment>, <comment>registerWorkerMap</comment>');
        $io->table(
            array('registerWorkerMap'),
            array(
                array('注册获取消息的consumer程序<info>[Register the consumer program to get the message]</info>'),
            )
        );
        $io->table(
            array('registerEvents'),
            array(
                array('注册发送消息的event和对应的处理listener<info>[Register the event of the message and the corresponding handle listener]</info>'),
            )
        );
        $io->listing(array(
            '<fg=white>请将你的业务消息consumer程序注册到WokerMap中<info>[Please register your business message consumer program into WokerMap]</info></>',
            '<fg=white>再创建一个worker与tasker沟通的event和处理listener<info>[And then create a worker and tasker communication event and handle listener]</info></>',
            "<fg=white>完成<info>done></info></>"
        ));
        $io->success('🙏感谢收看[thank you]');
    }

}
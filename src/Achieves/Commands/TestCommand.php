<?php

namespace Jue\Swoole\Achieves\Commands;


use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{
    private $msg;

    public function __construct($msg)
    {
        $this->msg = $msg;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('table展示可执行的consumer程序<info>[table display can consumer process]</info>');
        $io->section('使用例子<info>[example]</info>(php console swoole start class-course-schedule-for-order 2 1 3)');

        $map = [];
        foreach (SwooleMaster::getConfig()->getWorkerMap() as $key => $item)
        {
            $map[] = [$key, $item['class'], $item['des']];
        }
        $io->table(
            array('调用的命令[command]', '执行类[class]', '描述[desc]'),
            $map
        );
        $io->newLine(1);
    }
}
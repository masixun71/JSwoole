<?php

namespace Jue\Swoole\Achieves\Clients;


use Jue\Swoole\Domain\Clients\IClient;
use Jue\Swoole\Domain\Events\AbstractEvent;
use Jue\Swoole\Domain\Messages\Processor;
use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Language\Language;

class Client implements IClient
{

    public function fire(AbstractEvent $event)
    {
        $taskerTable = SwooleMaster::getTaskerTable();

        $index = $event->getIndex() % SwooleMaster::getTaskerCount()->get();

        logger()->info(Language::getWord(Language::SELECT_TASKER), [
            'index' => $event->getIndex(),
            'message' => Processor::toMessage($event)
        ]);


        $msgKey = $taskerTable->get($index)[SwooleMaster::TABLE_MSG_KEY];
        $messageQueue = msg_get_queue($msgKey, 0666);
        $res = msg_send($messageQueue, 1, $event, true);

        if($res)
        {
            logger()->info(Language::getWord(Language::CLIENT_SEND_MSG_SUCCESS), [
                'index' => $event->getIndex(),
                'message' => Processor::toMessage($event),
            ]);
        }
        else
        {
            logger()->info(Language::getWord(Language::CLIENT_SEND_MSG_FAIL), [
                'index' => $event->getIndex(),
                'message' => Processor::toMessage($event),
            ]);
        }
    }

}
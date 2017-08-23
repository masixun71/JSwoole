<?php

namespace Jue\Swoole\Domain\Messages;


use Jue\Swoole\Domain\Events\AbstractEvent;
use Jue\Swoole\Domain\ValueObjects\MessageDataVO;

class Processor
{

    public static function toMessage(AbstractEvent $event)
    {
        return (new MessageDataVO([
                'event_name' => $event->getClassName(),
                'parameters' => $event->toArray()
            ]
        ))->encode();
    }

    /**
     * @param $data
     * @return MessageDataVO
     */
    public static function getMessage($data)
    {
            $eventData = json_decode($data, true);

            return new MessageDataVO($eventData);
    }
}
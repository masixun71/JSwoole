<?php

namespace Jue\Swoole\Achieves\Channels;


use Jue\Swoole\Domain\Events\AbstractEvent;
use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Language\Language;

class Channel
{
    public static function push(AbstractEvent $event, $workerId, $workerType) {
        if ($event instanceof AbstractEvent) {
            $channel = SwooleMaster::getChannel();
            $res = $channel->push($event);

            if (!$res) {
                logger()->notice(Language::getWord(Language::SEND_CHANNEL_FAIL), [
                    'event' => $event,
                    'worker_id' => $workerId,
                    'worker_type' => $workerType
                ]);
            } else {
                logger()->info(Language::getWord(Language::SEND_CHANNEL_SUCCESS), [
                    'event' => $event,
                    'worker_id' => $workerId,
                    'worker_type' => $workerType
                ]);
            }
        }

    }
}
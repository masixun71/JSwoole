<?php

namespace Jue\Swoole\Achieves\Channels;


use Jue\Swoole\Domain\Events\AbstractEvent;
use Jue\Swoole\Achieves\Masters\SwooleMaster;

class Channel
{
    public static function push(AbstractEvent $event, $workerId, $workerType) {
        if ($event instanceof AbstractEvent) {
            $channel = SwooleMaster::getChannel();
            $res = $channel->push($event);

            if (!$res) {
                logger()->notice('发送进channel失败', [
                    'event' => $event,
                    'worker_id' => $workerId,
                    'worker_type' => $workerType
                ]);
            } else {
                logger()->info('发送进channel成功', [
                    'event' => $event,
                    'worker_id' => $workerId,
                    'worker_type' => $workerType
                ]);
            }
        }

    }
}
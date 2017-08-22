<?php

namespace Jue\Swoole\Achieves\Tables;


use Jue\Swoole\Achieves\Masters\SwooleMaster;

class MarkTable
{
    public static function markTable($message, $key)
    {
        $table = SwooleMaster::getMarkTable();
        $table->set($key, [
            SwooleMaster::TABLE_MARK_KEY => $message
        ]);

        logger()->info("当前在标记table中种下了标记", [
            'message' => $message,
            'key' => $key,
            'value' => $table->get($key)
        ]);

    }

    public static function clearMarkTable($key)
    {
        $table = SwooleMaster::getMarkTable();
        $table->set($key, [
            SwooleMaster::TABLE_MARK_KEY => null
        ]);
        logger()->info("当前在标记table中清除了标记", [
            'key' => $key,
            'value' => $table->get($key)
        ]);
    }

}
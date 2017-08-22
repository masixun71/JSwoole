<?php

namespace Jue\Swoole\Achieves\Tables;


use Jue\Swoole\Achieves\Masters\SwooleMaster;

class CollectTable
{

    public static function incrTable($key, $column, $message)
    {
        $table = SwooleMaster::getTable();
        $table->incr($key, $column);
        $data = $table->get($key);

        logger()->info("统计table——incr操作记录", [
            'message' => $message,
            'column' => $column,
            'times' => $data[$column]
        ]);
    }

    public static function decrTable($key, $column, $message)
    {
        $table = SwooleMaster::getTable();
        $table->decr($key, $column);
        $data = $table->get($key);

        logger()->info("统计table——decr操作记录", [
            'message' => $message,
            'column' => $column,
            'times' => $data[$column]
        ]);
    }
}
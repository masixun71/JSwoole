<?php

namespace Jue\Swoole\Achieves\Tables;


use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Language\Language;

class CollectTable
{

    public static function incrTable($key, $column, $message)
    {
        $table = SwooleMaster::getTable();
        $table->incr($key, $column);
        $data = $table->get($key);

        logger()->info(Language::getWord(Language::COLLECT_TABLE_INCR), [
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

        logger()->info(Language::getWord(Language::COLLECT_TABLE_DECR), [
            'message' => $message,
            'column' => $column,
            'times' => $data[$column]
        ]);
    }
}
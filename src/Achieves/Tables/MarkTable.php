<?php

namespace Jue\Swoole\Achieves\Tables;


use Jue\Swoole\Achieves\Masters\SwooleMaster;
use Jue\Swoole\Language\Language;

class MarkTable
{
    public static function markTable($message, $key)
    {
        $table = SwooleMaster::getMarkTable();
        $table->set($key, [
            SwooleMaster::TABLE_MARK_KEY => $message
        ]);

        logger()->info(Language::getWord(Language::MARK_TABLE_SET), [
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
        logger()->info(Language::getWord(Language::MARK_TABLE_CLEAR), [
            'key' => $key,
            'value' => $table->get($key)
        ]);
    }

}
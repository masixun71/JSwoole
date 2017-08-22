<?php

namespace Jue\Swoole\Achieves\Tables;


use Jue\Swoole\Achieves\Masters\SwooleMaster;

class MemoryTable
{
    public static function setMemory($memory, $key, $id)
    {
        $table = SwooleMaster::getMemoryTable();
        $table->set($key, [
            SwooleMaster::TABLE_MEMORY_KEY => $memory,
            'id' => $id
        ]);
    }

    public static function clearMemory($key)
    {
        $table = SwooleMaster::getMemoryTable();
        $table->set($key, [
            SwooleMaster::TABLE_MEMORY_KEY => 0
        ]);
        logger()->info("当前在内存table中clear", [
            'key' => $key,
            'value' => $table->get($key)
        ]);
    }

}
<?php

namespace Jue\Swoole\Achieves\Masters;



use Jue\Swoole\Achieves\Configs\Config;
use Jue\Swoole\Language\Language;

class SwooleMaster
{
    private static $taskers;
    private static $table;
    private static $markTable;
    private static $memoryTable;
    private static $taskerTable;
    private static $taskerCount;
    private static $channel;
    private static $topic;
    /** @var Config $config */
    private static $config;

    const SWOOLE_MANAGER_NAME = 'swoole-%s-manager';
    const SWOOLE_WORKER_NAME =  'swoole-%s-worker#%d';
    const SWOOLE_TASKER_NAME =  'swoole-%s-tasker#%d';


    const TABLE_TASKER_COLLECT_COUNT = 'swoole-table-tasker-collect-count-tasker#%d';
    const TABLE_TASKER_MARK_SET = 'swoole-table-tasker-mark-tasker#%d';
    const TABLE_TASKER_MEMORY_SET = 'swoole-table-tasker-memory-tasker#%d';

    const TABLE_RECIVE_KEY = 'recive';
    const TABLE_FINISH_KEY = 'finish';
    const TABLE_SUCCESS_KEY = 'success';
    const TABLE_FAIL_KEY = 'fail';

    const TABLE_MEMORY_KEY = 'memory';
    const TABLE_ID_KEY = 'id';
    const TABLE_MSG_KEY = 'msg_key';

    const TABLE_MARK_KEY = 'mark';


    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }


    public static function setTaskers($taskers)
    {
        self::$taskers = $taskers;
    }

    public static function getTaskers()
    {
        return self::$taskers;
    }

    public static function setTable(\swoole_table $table)
    {
        self::$table = $table;
    }

    public static function setMemoryTable(\swoole_table $table)
    {
        self::$memoryTable = $table;
    }

    /**
     * @return \swoole_table
     */
    public static function getMemoryTable()
    {
        return self::$memoryTable;
    }

    /**
     * @return \swoole_atomic
     */
    public static function getTaskerCount()
    {
        return self::$taskerCount;
    }

    public static function setTaskerCount(\swoole_atomic $taskerCount)
    {
        self::$taskerCount = $taskerCount;
    }



    public static function setTaskerTable(\swoole_table $table)
    {
        self::$taskerTable = $table;
    }

    /**
     * @return \swoole_table
     */
    public static function getTaskerTable()
    {
        return self::$taskerTable;
    }


    public static function setMarkTable(\swoole_table $table)
    {
        self::$markTable = $table;
    }

    /**
     * @return \swoole_table
     */
    public static function getMarkTable()
    {
        return self::$markTable;
    }


    /**
     * @return \swoole_table
     */
    public static function getTable()
    {
        return self::$table;
    }

    public static function setChannel($channel)
    {
        self::$channel = $channel;
    }

    public static function getChannel()
    {
        return self::$channel;
    }


    public static function getConfig()
    {
        return self::$config;
    }

    public static function setConfig($config)
    {
        self::$config = $config;
    }


    public static function getListener($eventName)
    {
        logger()->info(Language::getWord(Language::USED_LISTENER), [
            'listener' => self::getConfig()->getListeners()[$eventName],
        ]);
        return container()->make(self::getConfig()->getListeners()[$eventName]);
    }

    /**
     * @return mixed
     */
    public static function getTopic()
    {
        return self::$topic;
    }

    /**
     * @param mixed $topic
     */
    public static function setTopic($topic)
    {
        self::$topic = $topic;
    }

}
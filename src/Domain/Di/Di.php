<?php

namespace Jue\Swoole\Domain\Di;

class Di
{

    private static $di = [];


    public static function set($key, $value)
    {
        self::$di[$key] = $value;
    }

    public static function get($key)
    {
        return self::$di[$key];
    }

    public static function remove($key)
    {
        unset(self::$di[$key]);
    }

}
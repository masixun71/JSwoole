<?php

namespace Jue\Swoole\Achieves\Loggers;


use Jue\Swoole\Domain\Loggers\ILoggerManagerInterface;

class LoggerManager implements ILoggerManagerInterface
{

    public static function newLogger($dir, $extName = '')
    {
        return Logger::getInstance($dir, $extName);
    }

}
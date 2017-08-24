<?php

namespace Jue\Swoole\Domain\Loggers;


interface ILoggerManagerInterface
{
    public static function newLogger($dir, $extName = '');
}
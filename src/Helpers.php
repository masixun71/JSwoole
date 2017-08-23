<?php

use Illuminate\Contracts\Container\Container;
use Jue\Swoole\Domain\Loggers\ILogger;

if (!function_exists('logger')) {

    /**
     * 读取logger实例.
     *
     * @param string $make 服务名称
     *
     * @return ILogger
     */
    function logger()
    {
        $make = ILogger::class;

        if ($logger = container()->make($make)) {
            return $logger;
        } else {
            throw new \Symfony\Component\Console\Exception\LogicException('container not exist logger');
        }
    }
}

if (!function_exists('container')) {

    /**
     * 读取container实例.
     *
     * @param string $make 服务名称
     *
     * @return Container
     */
    function container()
    {
        return \Illuminate\Container\Container::getInstance();
    }
}

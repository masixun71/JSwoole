<?php

use Jue\Swoole\Domain\Clients\IClient;
use Jue\Swoole\Domain\Di\Di;
use Jue\Swoole\Domain\Loggers\ILogger;

if (!function_exists('logger')) {

    /**
     * 读取logger实例.
     *
     * @param string $make 服务名称
     *
     * @return ILogger
     */
    function logger($make = null)
    {
        if (is_null($make)) {
            $make = 'logger';
        }

        if ($logger = Di::get($make)) {
            return $logger;
        } else {
            throw new \Symfony\Component\Console\Exception\LogicException('di not exist logger');
        }
    }
}

if (!function_exists('client')) {

    /**
     * 读取logger实例.
     *
     * @param string $make 服务名称
     *
     * @return IClient
     */
    function client($make = null)
    {
        if (is_null($make)) {
            $make = 'client';
        }

        if ($logger = Di::get($make)) {
            return $logger;
        } else {
            throw new \Symfony\Component\Console\Exception\LogicException('di not exist client');
        }
    }
}
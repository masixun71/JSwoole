<?php

namespace Jue\Swoole\Achieves\Loggers;


use Illuminate\Contracts\Support\Arrayable;
use Jue\Swoole\Domain\Loggers\ILogger;

class LoggerWriter implements ILogger
{
    private $monolog;

    public function __construct(\Monolog\Logger $logger)
    {
        $this->monolog = $logger;
    }

    /**
     * 运行时出现的错误，例如程序组件不可用或者出现非预期的异常、数据库不可用了或者其他的情况下.
     *
     * @param string $message
     * @param array|object $context
     *
     * @return bool
     */
    public function error($message, $context = [])
    {
        return $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * 出现非错误性的异常，例如使用了被弃用的API、错误地使用了API或者非预想的不必要错误。.
     *
     * @param string $message
     * @param array|object $context
     *
     * @return bool
     */
    public function warning($message, $context = [])
    {
        return $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * 正常但是重要需要标记的事件.
     *
     * @param string $message
     * @param array|object $context
     *
     * @return bool
     */
    public function notice($message, $context = [])
    {
        return $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * 正常事件，例如用户登录和SQL记录.
     *
     * @param string $message
     * @param array|object $context
     *
     * @return bool
     */
    public function info($message, $context = [])
    {
        return $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * 打印调试信息.
     *
     * @param string $message
     * @param array|object $context
     *
     * @return bool
     */
    public function debug($message, $context = [])
    {
        return $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * 最终写日志的方法.
     *
     * @param string $level
     * @param string $message
     * @param array|object $context
     *
     * @return bool
     */
    private function writeLog($level, $message, $context)
    {
        return $this->monolog->{$level}($message, $this->formatContext($context));
    }

    /**
     * Format the parameters for the logger.
     *
     * @param mixed $context
     *
     * @return mixed
     */
    protected function formatContext($context)
    {
        if (is_array($context)) {
            return $context;
        } elseif ($context instanceof Arrayable || method_exists($context, 'toArray')) {
            return $context->toArray();
        } elseif ($context instanceof \Exception) {
            $ret = $this->getFullExceptionAsArray($context);
            $ret['exception'] = $context;
            return $ret;
        } elseif (is_object($context)) {
            return get_object_vars($context);
        }

        return (array)$context;
    }

    public function getFullExceptionAsArray(\Exception $e)
    {
        $result = [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'exception_class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'previous' => $e->getPrevious(),
            'trace' => [],
        ];
        $count = 0;
        foreach ($e->getTrace() as $frame) {
            $args = '';
            if (isset($frame['args'])) {
                $args = [];
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $args[] = "'" . $arg . "'";
                    } elseif (is_array($arg)) {
                        $args[] = 'Array';
                    } elseif (is_null($arg)) {
                        $args[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $args[] = ($arg) ? 'true' : 'false';
                    } elseif (is_object($arg)) {
                        $args[] = get_class($arg);
                    } elseif (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    } else {
                        $args[] = $arg;
                    }
                }
                $args = implode(', ', $args);
            }
            $result['trace']['#' . $count] = sprintf('%s(%s): %s(%s)',
                isset($frame['file']) ? $frame['file'] : '',
                isset($frame['line']) ? $frame['line'] : '',
                isset($frame['function']) ? $frame['function'] : '',
                $args);
            ++$count;
        }

        return $result;
    }
}
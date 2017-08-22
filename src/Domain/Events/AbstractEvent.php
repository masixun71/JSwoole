<?php

namespace Jue\Swoole\Domain\Events;


class AbstractEvent
{
    protected $index;



    public function __construct($index)
    {
        $this->index = $index;
    }

    public function getIndex()
    {
        return $this->index;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $class = new \ReflectionClass(static::class);
        $parameters = $class->getProperties(\ReflectionProperty::IS_PROTECTED);
        $arr = [];

        foreach ($parameters as $parameter)
        {
            $key = $parameter->getName();
            $arr[$key] = $this->$key;
        }

        return $arr;
    }



    public function getClassName()
    {
        return static::class;
    }
}
<?php

namespace Jue\Swoole\Domain\Events;


class TestEvent extends AbstractEvent
{


    protected $userId;


    public function __construct($userId, $index)
    {
        parent::__construct($index);
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }





}
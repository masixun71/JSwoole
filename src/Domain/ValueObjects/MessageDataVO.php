<?php

namespace Jue\Swoole\Domain\ValueObjects;




class MessageDataVO
{

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }


    public function getParameter()
    {
        if(isset($this->getData()['parameters']))
        {
            return $this->getData()['parameters'];
        }
        else
        {
            return null;
        }
    }


    public function getEventName()
    {
        if(isset($this->getData()['event_name']))
        {
            return $this->getData()['event_name'];
        }
        else
        {
            return null;
        }
    }

    public function getListenerName()
    {
        if(isset($this->getData()['listener_name']))
        {
            return $this->getData()['listener_name'];
        }
        else
        {
            return null;
        }
    }

    public function addListener($listenerName)
    {
        $this->data['listener_name'] = $listenerName;
    }


    /**
     */
    public function buildEvent()
    {
        return app($this->getEventName(), $this->getParameter());
    }

    public function buildListener()
    {
        return app($this->getListenerName());
    }


    public function encode($mode = 'json')
    {
        switch ($mode){
            case 'json':
                return json_encode($this->getData());
                break;
            default:
                return null;
                break;
        }
    }
}
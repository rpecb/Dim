<?php

class Factory extends Service
{
    protected $callable;

    public function __construct($callable, $arguments = array())
    {
        if (!is_callable($callable)) {
            throw new InvalidArgumentException('A callable expected.');
        }
        $this->callable = $callable;
        $this->arguments = is_array($arguments) ? $arguments : array($arguments);
    }

    public function get($arguments = array())
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        return $this->resolveCallable($this->callable, $arguments + $this->arguments);
    }
}
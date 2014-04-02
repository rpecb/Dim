<?php

class Factory extends Service
{
    protected $callable;

    public function __construct($class, $callable, $arguments = array())
    {
        parent::__construct($class, $arguments);
        if (!is_callable($callable)) {
            throw new InvalidArgumentException('A callable expected.');
        }
        $this->callable = $callable;
    }

    public function get($arguments = array(), Dim $dim = null)
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        return $this->resolveCallable($this->callable, $arguments + $this->arguments, $dim);
    }
}
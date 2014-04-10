<?php

class Factory extends Service
{
    protected $callable;

    public function __construct($class, $callable, $arguments = null)
    {
        parent::__construct($class, $arguments);
        if (!is_callable($callable)) {
            throw new InvalidArgumentException('A callable expected.');
        }
        $this->callable = $callable;
    }

    public function get($arguments = null, Dim $dim = null)
    {
        return static::resolveCallable($this->callable, (array)$arguments + $this->arguments, $dim);
    }
}
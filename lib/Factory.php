<?php

class Factory extends Service
{
    protected $callable;

    public function __construct(Dim $dim, $callable, $arguments = array())
    {
        if (!is_callable($callable)) {
            throw new InvalidArgumentException('A callable expected.');
        }
        $this->dim = $dim;
        $this->callable = $callable;
        $this->arguments = is_array($arguments) ? $arguments : array($arguments);
    }

    public function get($arguments = array())
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        return $this->resolveCallable($this->callable, array_merge($this->arguments, $arguments));
    }
}
<?php

class Extension extends Factory
{
    protected $service;

    public function __construct(Service $service, $callable, $arguments = array()) #
    {
        parent::__construct($service->getClass(), $callable, $arguments);
        $this->service = $service;
    }

    public function get($arguments = array(), Dim $dim = null) #
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        return static::resolveCallable(
            $this->callable,
            $arguments + $this->arguments + array($this->service->get()),
            $dim
        );
    }
} 